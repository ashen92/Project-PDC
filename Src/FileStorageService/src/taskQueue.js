import Queue from "bull";
import { uploadFileToAzure } from "./azureUpload.js";

const redisHost = process.env.REDIS_HOST;
const redisPort = process.env.REDIS_PORT;

const uploadQueue = new Queue("file-upload-queue", `redis://${redisHost}:${redisPort}`);

uploadQueue.process(async (job) => {
    await uploadFileToAzure(job.data.files);
});

uploadQueue.on("failed", (job, err) => {
    console.error("Job failed", job.id, job.data, err);
});

export const addFileToUploadQueue = async (files) => {
    await uploadQueue.add({ files });
};
