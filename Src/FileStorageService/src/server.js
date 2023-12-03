import http from "http";
import { parseIncomingFile } from "./fileParser.js";
import { uploadFileToAzure } from "./azureUpload.js";

const server = http.createServer(async (req, res) => {
    res.setHeader("Content-Security-Policy", "default-src 'none'; script-src 'self'; connect-src 'self'; img-src 'self'; style-src 'self';");
    res.setHeader("X-Frame-Options", "DENY");
    res.setHeader("X-Content-Type-Options", "nosniff");
    res.setHeader("X-XSS-Protection", "1; mode=block");

    if (req.method === "POST" && req.url === "/upload") {
        try {
            const files = await parseIncomingFile(req);
            await uploadFileToAzure(files);
            res.writeHead(200);
            res.end(
                JSON.stringify(
                    {
                        message: "File upload initiated"
                    }
                )
            );
        } catch (error) {
            console.error("Error parsing file:", error);
            res.writeHead(500);
            res.end("Internal Server Error");
        }
    } else {
        res.writeHead(404);
        res.end("Not Found");
    }
});

const port = 3000;
server.listen(port, () => console.log(`Server running on port ${port}`));
