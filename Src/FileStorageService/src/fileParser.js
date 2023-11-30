import formidable from "formidable";

export async function parseIncomingFile(req) {
    return new Promise((resolve, reject) => {
        const form = new formidable.IncomingForm();

        form.parse(req, (err, fields, files) => {
            if (err) {
                console.error("Error parsing file:", err);
                reject(err);
                return;
            }
            resolve(files);
        });
    });
}
