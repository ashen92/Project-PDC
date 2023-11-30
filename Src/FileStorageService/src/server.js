import http from "http";
import { parseIncomingFile } from "./fileParser.js";
import { addTaskToQueue } from "./taskQueue.js";

const server = http.createServer(async (req, res) => {
    if (req.method === "POST" && req.url === "/upload") {
        try {
            const file = await parseIncomingFile(req);
            await addTaskToQueue(file);
            res.writeHead(200);
            res.end("File upload initiated");
        } catch (error) {
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
