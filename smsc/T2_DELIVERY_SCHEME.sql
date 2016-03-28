CREATE OR REPLACE VIEW T2_DELIVERY_SCHEME
(
   DELIVERY_ID,
   DELIVERY_NM,
   DELIVERY_AB,
   ATTEMPT,
   NETWORK_ID,
   NETWORK_NM,
   ERROR_ID,
   ERROR_MAP,
   ERROR_NM,
   DELAY,
   DELAY_SRC
)
AS
     SELECT DELIVERY_ID,
            DELIVERY_NM,
            DELIVERY_AB,
            ATTEMPT || ' - ...' ATTEMPT,
            network_id,
            NETWORK_NM,
            error_id,
            CASE WHEN NOT network_id = 4 THEN ERROR_MAP ELSE '' END
               AS "ERROR_MAP",
            ERROR_NM,
            delay,
            delay_src
       FROM (SELECT t1.*,
                    DECODE (delay_source,
                            0, 'Базовая схема',
                            1, 'Переопределено',
                            2, 'Фиксированый интервал',
                            'КАКАЯ-ТО ХРЕНЬ')
                       delay_src,
                    RANK ()
                    OVER (
                       PARTITION BY DELIVERY_NM,
                                    DELIVERY_AB,
                                    ATTEMPT,
                                    NETWORK_NM,
                                    error_id,
                                    ERROR_NM
                       ORDER BY delay_source DESC)
                       AS rnk
               FROM (                                 --базовая схема доставки
                     SELECT DV.DELIVERY_ID,
                            dv.DELIVERY_NM,
                            dv.DELIVERY_AB,
                            a.ATTEMPT_ID,
                            a.ATTEMPT,
                            n.network_id,
                            n.NETWORK_NM,
                            e.error_id,
                            TRIM (
                                  TO_CHAR (
                                     TO_NUMBER (
                                        SUBSTR (
                                           TRIM (TO_CHAR (e.error_id, 'XXXX')),
                                           1,
                                             LENGTH (
                                                TRIM (
                                                   TO_CHAR (e.error_id, 'XXXX')))
                                           - 2),
                                        'XX'))
                               || ' '
                               || TO_CHAR (
                                     TO_NUMBER (
                                        SUBSTR (
                                           TRIM (TO_CHAR (e.error_id, 'XXXX')),
                                             LENGTH (
                                                TRIM (
                                                   TO_CHAR (e.error_id, 'XXXX')))
                                           - 2
                                           + 1,
                                           2),
                                        'XX')))
                               AS "ERROR_MAP",
                            e.ERROR_NM,
                            d.delay,
                            0 delay_source
                       FROM attempt a,
                            delivery dv,
                            delay d,
                            error e,
                            network n
                      WHERE     A.DELIVERY_ID = DV.DELIVERY_ID
                            AND A.ATTEMPT_ID = D.ATTEMPT_ID
                            AND D.ERROR_ID = E.ERROR_ID
                            AND D.NETWORK_ID = E.NETWORK_ID
                            AND E.NETWORK_ID = N.NETWORK_ID
                            AND Dv.DELIVERY_NM = '_'
                     UNION ALL
                     --все базовые задержки для всех ошибок доставки из профиля по умочанию на все диапазоны доставки с нефиксированной задержкой доставки
                     SELECT DELIVERY_ID,
                            DELIVERY_NM,
                            DELIVERY_AB,
                            ATTEMPT_ID,
                            ATTEMPT,
                            network_id,
                            NETWORK_NM,
                            error_id,
                            ERROR_MAP,
                            ERROR_NM,
                            delay,
                            0 delay_source
                       FROM (SELECT DV.DELIVERY_ID,
                                    Dv.DELIVERY_NM,
                                    DV.DELIVERY_AB,
                                    A.ATTEMPT_ID,
                                    A.ATTEMPT
                               FROM attempt a, delivery dv
                              WHERE     A.DELIVERY_ID = Dv.DELIVERY_ID
                                    AND Dv.DELIVERY_NM != '_'
                                    AND A.DELAY IS NULL),
                            (SELECT n.network_id,
                                    N.NETWORK_NM,
                                    e.error_id,
                                    TRIM (
                                          TO_CHAR (
                                             TO_NUMBER (
                                                SUBSTR (
                                                   TRIM (
                                                      TO_CHAR (e.error_id,
                                                               'XXXX')),
                                                   1,
                                                     LENGTH (
                                                        TRIM (
                                                           TO_CHAR (e.error_id,
                                                                    'XXXX')))
                                                   - 2),
                                                'XX'))
                                       || ' '
                                       || TO_CHAR (
                                             TO_NUMBER (
                                                SUBSTR (
                                                   TRIM (
                                                      TO_CHAR (e.error_id,
                                                               'XXXX')),
                                                     LENGTH (
                                                        TRIM (
                                                           TO_CHAR (e.error_id,
                                                                    'XXXX')))
                                                   - 2
                                                   + 1,
                                                   2),
                                                'XX')))
                                       AS "ERROR_MAP",
                                    E.ERROR_NM,
                                    d.delay
                               FROM attempt a,
                                    delivery dv,
                                    delay d,
                                    error e,
                                    network n
                              WHERE     A.DELIVERY_ID = DV.DELIVERY_ID
                                    AND A.ATTEMPT_ID = D.ATTEMPT_ID
                                    AND D.ERROR_ID = E.ERROR_ID
                                    AND D.NETWORK_ID = E.NETWORK_ID
                                    AND E.NETWORK_ID = N.NETWORK_ID
                                    AND Dv.DELIVERY_NM = '_')
                     UNION ALL
                     --все переопределенные задержки
                     SELECT DV.DELIVERY_ID,
                            dv.DELIVERY_NM,
                            dv.DELIVERY_AB,
                            a.ATTEMPT_ID,
                            a.ATTEMPT,
                            n.network_id,
                            n.NETWORK_NM,
                            e.error_id,
                            TRIM (
                                  TO_CHAR (
                                     TO_NUMBER (
                                        SUBSTR (
                                           TRIM (TO_CHAR (e.error_id, 'XXXX')),
                                           1,
                                             LENGTH (
                                                TRIM (
                                                   TO_CHAR (e.error_id, 'XXXX')))
                                           - 2),
                                        'XX'))
                               || ' '
                               || TO_CHAR (
                                     TO_NUMBER (
                                        SUBSTR (
                                           TRIM (TO_CHAR (e.error_id, 'XXXX')),
                                             LENGTH (
                                                TRIM (
                                                   TO_CHAR (e.error_id, 'XXXX')))
                                           - 2
                                           + 1,
                                           2),
                                        'XX')))
                               AS "ERROR_MAP",
                            e.ERROR_NM,
                            d.delay,
                            1 delay_source
                       FROM attempt a,
                            delivery dv,
                            delay d,
                            error e,
                            network n
                      WHERE     A.DELIVERY_ID = DV.DELIVERY_ID
                            AND A.ATTEMPT_ID = D.ATTEMPT_ID
                            AND D.ERROR_ID = E.ERROR_ID
                            AND D.NETWORK_ID = E.NETWORK_ID
                            AND E.NETWORK_ID = N.NETWORK_ID
                            AND Dv.DELIVERY_NM != '_'
                     UNION ALL
                     --все фиксированные задержки для всех временных (DELAY>0) ошибок доставки профиля по умочанию (!!!возможо Беркут исключает постоянные ошибке не от профиля по умолчанию, а он предыдущей попытки доставки, но это мудрить с запросом - ЛЕНЬ)
                     SELECT DELIVERY_ID,
                            DELIVERY_NM,
                            DELIVERY_AB,
                            ATTEMPT_ID,
                            ATTEMPT,
                            network_id,
                            NETWORK_NM,
                            error_id,
                            ERROR_MAP,
                            ERROR_NM,
                            delay,
                            2 delay_source
                       FROM (SELECT DV.DELIVERY_ID,
                                    Dv.DELIVERY_NM,
                                    DV.DELIVERY_AB,
                                    A.ATTEMPT_ID,
                                    A.ATTEMPT,
                                    A.DELAY
                               FROM attempt a, delivery dv
                              WHERE     A.DELIVERY_ID = DV.DELIVERY_ID
                                    AND Dv.DELIVERY_NM != '_'
                                    AND a.delay IS NOT NULL),
                            (SELECT n.network_id,
                                    N.NETWORK_NM,
                                    e.error_id,
                                    TRIM (
                                          TO_CHAR (
                                             TO_NUMBER (
                                                SUBSTR (
                                                   TRIM (
                                                      TO_CHAR (e.error_id,
                                                               'XXXX')),
                                                   1,
                                                     LENGTH (
                                                        TRIM (
                                                           TO_CHAR (e.error_id,
                                                                    'XXXX')))
                                                   - 2),
                                                'XX'))
                                       || ' '
                                       || TO_CHAR (
                                             TO_NUMBER (
                                                SUBSTR (
                                                   TRIM (
                                                      TO_CHAR (e.error_id,
                                                               'XXXX')),
                                                     LENGTH (
                                                        TRIM (
                                                           TO_CHAR (e.error_id,
                                                                    'XXXX')))
                                                   - 2
                                                   + 1,
                                                   2),
                                                'XX')))
                                       AS "ERROR_MAP",
                                    E.ERROR_NM
                               FROM delay d, error e, network n
                              WHERE     D.ERROR_ID = E.ERROR_ID
                                    AND D.NETWORK_ID = E.NETWORK_ID
                                    AND E.NETWORK_ID = N.NETWORK_ID
                                    AND attempt_id IN
                                           (SELECT A.ATTEMPT_ID
                                              FROM attempt a, delivery dv
                                             WHERE     A.DELIVERY_ID =
                                                          DV.DELIVERY_ID
                                                   AND DV.DELIVERY_NM = '_')
                                    AND D.DELAY > 0)) t1)
      WHERE rnk = 1
   ORDER BY DELIVERY_id,
            network_id,
            error_id,
            attempt
