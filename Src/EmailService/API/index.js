import "dotenv/config";
import http from "http";
import { createClient } from "redis";
import { StringDecoder } from "string_decoder";

const redisClient = createClient();
redisClient.on("error", err => console.log("Redis Client Error", err));

async function connectClient() {
    try {
        await redisClient.connect();
    } catch (err) {
        console.error("Error connecting to Redis", err);
    }
}

connectClient();

// Create a HTTP server
const server = http.createServer((req, res) => {
    res.setHeader("Content-Security-Policy", "default-src 'none'; script-src 'self'; connect-src 'self'; img-src 'self'; style-src 'self';");
    res.setHeader("X-Frame-Options", "DENY");
    res.setHeader("X-Content-Type-Options", "nosniff");
    res.setHeader("X-XSS-Protection", "1; mode=block");

    // #region Rate limit

    redisClient.select(1)
        .then(() => {
            const ip = req.socket.remoteAddress;

            redisClient.get(ip)
                .then(record => {
                    // If the IP address has made too many requests, return a 429 response
                    if (record) {
                        record = parseInt(record);
                        if (record >= 5000) {
                            res.writeHead(429);
                            res.end("Too Many Requests");
                            return;
                        }
                    }

                    // Otherwise, increment the count for the IP address
                    redisClient.set(ip, (record || 0) + 1, "EX", 3600)
                        .then(() => {
                            // #region Handle API requests

                            redisClient.select(0)
                                .then(() => {
                                    // Parse the request URL
                                    const parsedUrl = new URL(req.url, `http://${req.headers.host}`);
                                    const path = parsedUrl.pathname;
                                    const trimmedPath = path.replace(/^\/+|\/+$/g, "");

                                    // Check for POST request to specific path
                                    if (trimmedPath === "api/emails" && req.method === "POST") {
                                        // Check the API key
                                        const apiKey = req.headers["x-api-key"]; // Assuming API key is sent in 'x-api-key' header
                                        if (apiKey !== process.env.PDC_WEBSITE_API_KEY) {
                                            res.writeHead(403);
                                            res.end("Invalid API key");
                                            return;
                                        }

                                        // Handle POST data
                                        const decoder = new StringDecoder("utf-8");
                                        let buffer = "";

                                        req.on("data", (data) => {
                                            buffer += decoder.write(data);
                                        });

                                        req.on("end", () => {
                                            buffer += decoder.end();

                                            // Parse the email data
                                            let emailData;
                                            try {
                                                emailData = JSON.parse(buffer);
                                            } catch (e) {
                                                res.writeHead(400);
                                                res.end("Invalid JSON");
                                                return;
                                            }

                                            // Queue the email data in Redis
                                            redisClient.lPush("emailQueue", JSON.stringify(emailData))
                                                .then(reply => {
                                                    res.writeHead(201, { "Content-Type": "application/json" });
                                                    res.end(JSON.stringify({ message: "Email queued successfully" }));
                                                })
                                                .catch(err => {
                                                    res.writeHead(500);
                                                    res.end("Error queuing email");
                                                });
                                        });
                                    } else {
                                        res.writeHead(404);
                                        res.end("Not Found");
                                    }
                                })
                                .catch(err => {
                                    console.error("Error selecting Redis database", err);
                                });

                            // #endregion
                        })
                        .catch(err => {
                            res.writeHead(500);
                            res.end("Error updating rate limit record");
                            return;
                        });
                })
                .catch(err => {
                    res.writeHead(500);
                    res.end("Error reading rate limit record");
                    return;
                });
        })
        .catch(err => {
            console.error("Error selecting Redis database", err);
        });

    // #endregion
});

// Start the server
const PORT = 3000;
server.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
});