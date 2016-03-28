CREATE OR REPLACE VIEW T2_PROVIDERS
(
   NAME,
   STATUS,
   EMAIL,
   DSC,
   CREATED,
   MODIFIED,
   SYSTEM_ID,
   PASS,
   TRANSPORT
)
AS
     SELECT CP.NAME NAME,
            DECODE (CP.STATUS,
                    0, 'Свободен',
                    1, 'Активен',
                    3, 'Отключен',
                    NULL, NULL,
                    CP.STATUS || ' - Неизвестен')
               STATUS,
            CP.EMAIL EMAIL,
            CP.DSC DSC,
            CP.REGISTRED CREATED,
            CP.MODIFIED MODIFIED,
            CPA.ACCOUNT_LOGIN SYSTEM_ID,
            CPA.PASS,
            DECODE (CPA.TRANSPORT_ID,
                    1, 'SMPP (SMS)',
                    2, 'SMPP (USSD)',
                    3, 'SMTP (SMS)',
                    4, 'MM7 (MMS)',
                    5, 'ParlayX (SMS,MMS)',
                    6, 'HTTP (WAP)',
                    7, 'HTTP (WIG)',
                    8, 'Voice (VOICE)',
                    9, 'SMPP (SMS,RBT)',
                    10, 'SMTP (SMS,RBT)',
                    11, 'HTTP_XML (SMS)',
                    12, 'HTTP (WAP-TRAF)',
                    NULL, NULL,
                    CPA.TRANSPORT_ID || ' - UNKNOWN')
               TRANSPORT
       FROM    (SELECT N,
                       STATUS,
                       NAME,
                       EMAIL,
                       DSC,
                       REGISTRED,
                       MODIFIED
                  FROM CPA_PROVIDER) CP
            LEFT JOIN
               (SELECT PROVIDER_N,
                       ACCOUNT_N,
                       ACCOUNT_LOGIN,
                       PASS,
                       TRANSPORT_ID
                  FROM CPA_PROVIDER_ACCOUNT) CPA
            ON CP.N = CPA.PROVIDER_N
   ORDER BY CP.NAME, CPA.ACCOUNT_LOGIN
