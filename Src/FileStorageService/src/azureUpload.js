import fs from "fs";
import "dotenv/config";
import { BlobServiceClient } from "@azure/storage-blob";
import { v1 as uuidv1 } from "uuid";

const AZURE_STORAGE_CONNECTION_STRING =
    process.env.AZURE_STORAGE_CONNECTION_STRING;

if (!AZURE_STORAGE_CONNECTION_STRING) {
    throw Error("Azure Storage Connection string not found");
}

// Create the BlobServiceClient object with connection string
const blobServiceClient = BlobServiceClient.fromConnectionString(
    AZURE_STORAGE_CONNECTION_STRING
);

export async function uploadFileToAzure(files) {
    files.file.forEach(async file => {
        var containerName = file.mimetype;
        containerName = containerName.replace(/\//g, "-").replace(/\*/g, "");
        const containerClient = blobServiceClient.getContainerClient(containerName);

        const containerExists = await containerClient.exists();
        if (!containerExists) {
            await containerClient.create();
            console.log(`Container "${containerName}" is created`);
        } else {
            console.log(`Container "${containerName}" already exists`);
        }

        const blobName = uuidv1() + ":" + file.originalFilename;
        const blockBlobClient = containerClient.getBlockBlobClient(blobName);

        console.log(
            `\nUploading to Azure storage as blob\n\tname: ${blobName}:\n\tURL: ${blockBlobClient.url}`
        );

        const data = fs.readFileSync(file.filepath);
        const uploadBlobResponse = await blockBlobClient.upload(data, data.length);
        console.log(
            `Blob was uploaded successfully. requestId: ${uploadBlobResponse.requestId}`
        );
    });
}
