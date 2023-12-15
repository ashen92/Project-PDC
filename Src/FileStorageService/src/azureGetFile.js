import { BlobServiceClient } from "@azure/storage-blob";
import "dotenv/config";

const AZURE_STORAGE_CONNECTION_STRING =
    process.env.AZURE_STORAGE_CONNECTION_STRING;

if (!AZURE_STORAGE_CONNECTION_STRING) {
    throw Error("Azure Storage Connection string not found");
}

const blobServiceClient = BlobServiceClient.fromConnectionString(
    AZURE_STORAGE_CONNECTION_STRING
);

export async function getFileFromAzure(filePath, writableStream) {
    const [containerName, blobName] = filePath.split("-uuid-");

    const containerClient = blobServiceClient.getContainerClient(containerName);
    const blobClient = containerClient.getBlobClient(blobName);

    if (!(await blobClient.exists())) {
        throw new Error(`File ${blobName} not found`);
    }

    const downloadResponse = await blobClient.download();

    downloadResponse.readableStreamBody.pipe(writableStream);
    console.log(`Download of ${blobName} succeeded`);
}