const { GoogleGenerativeAI } = require("@google/generative-ai");
require('dotenv').config();

const genAI = new GoogleGenerativeAI(process.env.GEMINI_API_KEY);
// Explicitly use gemini-2.5-flash which is available in this environment (Feb 2026)
const model = genAI.getGenerativeModel(
    { model: "gemini-2.5-flash" },
    { apiVersion: 'v1' }
);

/**
 * Generates a friendly auto-reply for a WhatsApp message.
 * @param {string} incomingMessage - The content of the received message.
 * @returns {Promise<string|null>} - The generated reply or null if failed.
 */
async function generateReply(incomingMessage) {
    if (!process.env.GEMINI_API_KEY) {
        console.warn("GEMINI_API_KEY is not set. Skipping auto-reply.");
        return null;
    }

    try {
        const prompt = `You are a helpful and friendly good friend. A user sent the following message on WhatsApp: "${incomingMessage}". 
        Please provide a short, friendly response. reply should according to the message(if sinhala reply in sinhala, if singlish reply in singlish)`;

        const result = await model.generateContent(prompt);
        const response = await result.response;
        return response.text().trim();
    } catch (error) {
        console.error("Error generating Gemini reply:", error.message);
        return null;
    }
}

module.exports = { generateReply };
