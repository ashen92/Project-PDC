import "dotenv/config";
import { createClient } from "redis";
import { EmailClient, KnownEmailSendStatus } from "@azure/communication-email";

const connectionString = process.env.COMMUNICATION_SERVICES_CONNECTION_STRING;
const emailClient = new EmailClient(connectionString);

const redisClient = createClient();
redisClient.on("error", err => console.log("Redis Client Error", err));

async function connectRedisClient() {
    try {
        await redisClient.connect();
    } catch (err) {
        console.error("Error connecting to Redis", err);
    }
}

async function processEmailQueue() {
    for (;;) {
        try {
            const emailJob = await redisClient.lPop("emailQueue");
            if (emailJob) {
                const emailData = JSON.parse(emailJob);
                sendEmail(emailData);
            } else {
                await new Promise(resolve => setTimeout(resolve, 1000)); // Wait for 1 second if queue is empty
            }
        } catch (err) {
            console.error("Error processing email queue:", err);
        }
    }
}

async function sendEmail(emailData) {
    const POLLER_WAIT_TIME = 10;
    try {
        const message = {
            senderAddress: process.env.FROM_EMAIL_ADDRESS,
            content: {
                subject: emailData.subject,
                plainText: emailData.body,
            },
            recipients: {
                to: [
                    {
                        address: emailData.to,
                        displayName: "Customer Name"
                    },
                ],
            },
        };

        const poller = await emailClient.beginSend(message);

        if (!poller.getOperationState().isStarted) {
            throw "Poller was not started.";
        }

        let timeElapsed = 0;
        while (!poller.isDone()) {
            poller.poll();
            console.log("Email send polling in progress");

            await new Promise(resolve => setTimeout(resolve, POLLER_WAIT_TIME * 1000));
            timeElapsed += 10;

            if (timeElapsed > 18 * POLLER_WAIT_TIME) {
                throw "Polling timed out.";
            }
        }

        if (poller.getResult().status === KnownEmailSendStatus.Succeeded) {
            console.log(`Successfully sent the email (operation id: ${poller.getResult().id})`);
        }
        else {
            throw poller.getResult().error;
        }
    } catch (e) {
        console.log(e);
    }
}

connectRedisClient();
processEmailQueue();