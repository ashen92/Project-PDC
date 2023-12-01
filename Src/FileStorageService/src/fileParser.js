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

            resolve(files);
        });
    });
}
