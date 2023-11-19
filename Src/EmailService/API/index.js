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

const server = http.createServer(async (req, res) => {
    res.setHeader("Content-Security-Policy", "default-src 'none'; script-src 'self'; connect-src 'self'; img-src 'self'; style-src 'self';");
    res.setHeader("X-Frame-Options", "DENY");
    res.setHeader("X-Content-Type-Options", "nosniff");
    res.setHeader("X-XSS-Protection", "1; mode=block");

    try {
        await redisClient.select(1);
        const ip = req.socket.remoteAddress;
        let record = await redisClient.get(ip);

        if (record) {
            record = parseInt(record);
            if (record >= 5000) {
                res.writeHead(429);
                res.end("Too Many Requests");
                return;
            }
        }

        await redisClient.set(ip, (record || 0) + 1, "EX", 3600);

        await redisClient.select(0);
        const parsedUrl = new URL(req.url, `http://${req.headers.host}`);
        const path = parsedUrl.pathname;
        const trimmedPath = path.replace(/^\/+|\/+$/g, "");

        if (trimmedPath === "api/emails" && req.method === "POST") {
            const apiKey = req.headers["x-api-key"];
            if (apiKey !== process.env.PDC_WEBSITE_API_KEY) {
                res.writeHead(403);
                res.end("Invalid API key");
                return;
            }

            const decoder = new StringDecoder("utf-8");
            let buffer = "";

            req.on("data", (data) => {
                buffer += decoder.write(data);
            });

            req.on("end", async () => {
                buffer += decoder.end();

                let emailData;
                try {
                    emailData = JSON.parse(buffer);
                } catch (e) {
                    res.writeHead(400);
                    res.end("Invalid JSON");
                    return;
                }

                if (!emailData.to || !emailData.subject || !emailData.body) {
                    res.writeHead(400);
                    res.end("Missing required fields (to, subject, body)");
                    return;
                }

                try {
                    await redisClient.lPush("emailQueue", JSON.stringify(emailData));
                    res.writeHead(201, { "Content-Type": "application/json" });
                    res.end(JSON.stringify({ message: "Email queued successfully" }));
                } catch (err) {
                    res.writeHead(500);
                    res.end("Error queuing email");
                }
            });
        } else {
            res.writeHead(404);
            res.end("Not Found");
        }
    } catch (err) {
        console.error("Error selecting Redis database", err);
    }
});

const PORT = 3000;
server.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
});