import { IncomingForm } from "formidable";

export async function parseIncomingFile(req) {
    return new Promise((resolve, reject) => {
        const form = new IncomingForm();

        form.parse(req, (err, _, files) => {
            if (err) {
                console.error("Error parsing file:", err);
                reject(err);
                return;
            }

            const filesArray = Object.values(files).flat();
            
            if (filesArray.length === 0) {
                reject(new Error("No files found in the request"));
                return;
            }

            resolve(filesArray);
        });
    });
}
