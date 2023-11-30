import Queue from "bull";
import { uploadFileToAzure } from "./azureUpload.js";

const uploadQueue = new Queue("file-upload-queue", "redis://127.0.0.1:6379");

uploadQueue.process(async (job) => {
    await uploadFileToAzure(job.data.file);
});

uploadQueue.on("failed", (job, err) => {
    console.error("Job failed", job.id, job.data, err);
});

export const addFileToUploadQueue = async (file) => {
    await uploadQueue.add({ file });
};
