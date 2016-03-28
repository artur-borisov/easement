CREATE OR REPLACE VIEW T2_SERVICES
(
   PROVIDER_NAME,
   PROVIDER_STATUS,
   SERVICE_NAME,
   SERVICE_STATUS,
   SERVICE_DESC,
   SERVICE_NUMBER,
   SERVICE_NUMBER_DEFAULT_FWD,
   SERVICE_TRAFFIC_TYPE,
   ACCOUNT_TRANSPORT_TYPE,
   SYSTEM_ID,
   ATTR_UNLIMPUSH,
   ATTR_CONFIRMAOC,
   ATTR_TARIFICATIONTYPE,
   ATTR_CHARGELEVEL,
   ATTR_CHARGELEVEL_COST,
   ATTR_CHARGELEVEL_COST_WO_TAX
)
AS
     SELECT P.NAME PROVIDER_NAME,
            DECODE (P.STATUS,
                    0, 'Свободен',
                    1, 'Активен',
                    3, 'Отключен',
                    NULL, NULL,
                    P.STATUS || ' - Неизвестен')
               PROVIDER_STATUS,
            N.SERVICE_NAME SERVICE_NAME,
            DECODE (N.STATUS,
                    0, 'Свободна',
                    1, 'Активна',
                    2, 'Заблокирована',
                    3, 'Отключена',
                    4, 'Тестовая',
                    5, 'Согласована',
                    NULL, NULL,
                    N.STATUS || ' - Неизвестна')
               SERVICE_STATUS,
            N.DSC SERVICE_DESC,
            SI.SERVICE_LOCATOR_VALUE SERVICE_NUMBER,
            SI.SERVICE_NUMBER SERVICE_NUMBER_DEFAULT_FWD,
            DECODE (SI.TRAFFIC_TYPE_ID,
                    1, 'SMS',
                    2, 'MMS',
                    4, 'WAP',
                    8, 'USSD',
                    32, 'VOICE',
                    NULL, NULL,
                    SI.TRAFFIC_TYPE_ID || ' - UNKNOWN')
               SERVICE_TRAFFIC_TYPE,
            DECODE (PA.TRANSPORT_ID,
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
                    PA.TRANSPORT_ID || ' - UNKNOWN')
               ACCOUNT_TRANSPORT_TYPE,
            PA.ACCOUNT_LOGIN SYSTEM_ID,
            AL_UNLIMPUSH.VALUE ATTR_UNLIMPUSH,
            AL_CONFIRMAOC.VALUE ATTR_CONFIRMAOC,
            AL_TARIFICATIONTYPE.VALUE ATTR_TARIFICATIONTYPE,
            DECODE (AL_CHARGELEVEL.VALUE,
                    NULL, '0 (системный)',
                    AL_CHARGELEVEL.VALUE)
               ATTR_CHARGELEVEL,
            DECODE (AL_CHARGELEVEL.COST, NULL, 0, AL_CHARGELEVEL.COST)
               ATTR_CHARGELEVEL_COST,
            DECODE (AL_CHARGELEVEL.COST_WO_TAX,
                    NULL, 0,
                    AL_CHARGELEVEL.COST_WO_TAX)
               ATTR_CHARGELEVEL_COST_WO_TAX
       FROM (SELECT *
               FROM cpa_number
              WHERE service_id != 0) n
            LEFT JOIN cpa_provider p
               ON N.UP = P.N
            LEFT JOIN CPA_SERVICE_IDENT si
               ON N.SERVICE_ID = SI.SERVICE_ID
            LEFT JOIN (SELECT ASI.SERVICE_IDENT_ID,
                              PA.TRANSPORT_ID,
                              PA.ACCOUNT_LOGIN
                         FROM CPA_ACCOUNT_SERVICE_IDENT ASI,
                              CPA_PROVIDER_ACCOUNT PA
                        WHERE PA.ACCOUNT_ID = ASI.ACCOUNT_ID) PA
               ON SI.ID = PA.SERVICE_IDENT_ID
            LEFT JOIN (SELECT AL.SERVICE_ID, AL.VALUE VALUE
                         FROM CPA_NUMBER_ATTR_LINK AL, CPA_NUMBER_ATTR A
                        WHERE     AL.AN = A.N
                              AND A.NAME = 'unlimPush'
                              AND AL.BLOCK = 0) AL_UNLIMPUSH
               ON N.SERVICE_ID = AL_UNLIMPUSH.SERVICE_ID
            LEFT JOIN (SELECT AL.SERVICE_ID, AL.VALUE VALUE
                         FROM CPA_NUMBER_ATTR_LINK AL, CPA_NUMBER_ATTR A
                        WHERE     AL.AN = A.N
                              AND A.NAME = 'confirmAoC'
                              AND AL.BLOCK = 0) AL_CONFIRMAOC
               ON N.SERVICE_ID = AL_CONFIRMAOC.SERVICE_ID
            LEFT JOIN (SELECT AL.SERVICE_ID, AL.VALUE VALUE
                         FROM CPA_NUMBER_ATTR_LINK AL, CPA_NUMBER_ATTR A
                        WHERE     AL.AN = A.N
                              AND A.NAME = 'tarifficationType'
                              AND AL.BLOCK = 0) AL_TARIFICATIONTYPE
               ON N.SERVICE_ID = AL_TARIFICATIONTYPE.SERVICE_ID
            LEFT JOIN (SELECT AL.SERVICE_ID,
                              AL.VALUE VALUE,
                              CL.VALUE COST,
                              CL.COST_WO_TAX
                         FROM CPA_NUMBER_ATTR_LINK AL,
                              CPA_NUMBER_ATTR A,
                              CPA_CHARGE_LEVEL CL
                        WHERE     AL.AN = A.N
                              AND AL.VALUE = CL.NAME
                              AND A.NAME = 'chargeLevel'
                              AND AL.BLOCK = 0) AL_CHARGELEVEL
               ON N.SERVICE_ID = AL_CHARGELEVEL.SERVICE_ID
      WHERE 1 = 1
   ORDER BY P.NAME,
            N.SERVICE_NAME,
            SI.SERVICE_LOCATOR_VALUE,
            SI.SERVICE_NUMBER
