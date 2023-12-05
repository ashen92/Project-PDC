import http from "http";
// import "dotenv/config";
import { parseIncomingFile } from "./fileParser.js";
import { uploadFileToAzure } from "./azureUpload.js";

const server = http.createServer(async (req, res) => {
    res.setHeader("Content-Security-Policy", "default-src 'none'; script-src 'self'; connect-src 'self'; img-src 'self'; style-src 'self';");
    res.setHeader("X-Frame-Options", "DENY");
    res.setHeader("X-Content-Type-Options", "nosniff");
    res.setHeader("X-XSS-Protection", "1; mode=block");

    if (req.method === "POST" && req.url === "/api/files") {
        try {
            const files = await parseIncomingFile(req);
            let fileProperties = await uploadFileToAzure(files);

            res.writeHead(200, { "Content-Type": "application/json" });
            res.end(
                JSON.stringify(
                    {
                        message: "Successfully uploaded files",
                        properties: fileProperties
                    })
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

const port = process.env.SERVER_PORT || 3000;
server.listen(port, () => console.log(`Server running on port ${port}`));
