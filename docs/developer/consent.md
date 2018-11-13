# Consent management


In respect of the General Data Protection Regulation (GDPR - EU 679/2016), the K-Box handle user consent for the following categories:

- General Privacy. The user must accept the privacy policy to enter and use the system
- Notification. The user can decide if wants to receive notifications or not

User consent is stored in the `consents` table with given or withdrawal timestamp. Evolution of the consent is stored in the `activities` table, under the `consent` log



