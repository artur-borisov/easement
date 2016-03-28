CREATE VIEW T2_ROUTE_TABLE AS
SELECT 
  NVL (GT_HLR_MASK,' ') GT_HLR_MASK,
  NVL (IMSI_MASK,' ') IMSI_MASK,
  NVL (MSISDN_MASK,' ') MSISDN_MASK,
  NVL (RN,' ') RN,
  NVL (CALLINGGT,' ') CALLINGGT,
  NVL (CALLEDGT,' ') CALEDGT,
  OPERATOR,
  GT_HLR_GROUP_NAME
FROM (
    SELECT 
      ghg.GT_HLR_MASK AS GT_HLR_MASK, DECODE (LENGTH (TRIM (TRANSLATE (ghg.GT_HLR_MASK, '$?', ' '))), NULL, 0, LENGTH (TRIM (TRANSLATE (ghg.GT_HLR_MASK, '$?', ' ')))) HLR_PREFIX_LENGTH,
      SUBSTR (TRANSLATE (ghg.GT_HLR_MASK, '?$ 0123456789', 'ab'), 1, 1) HLR_SUFIX_TYPE,
      DECODE (LENGTH (TRANSLATE (ghg.GT_HLR_MASK, '?$ 0123456789', 'ab')), NULL, 0, LENGTH (TRANSLATE (ghg.GT_HLR_MASK, '?$ 0123456789', 'ab'))) HLR_SUFIX_LENGTH,
      ig.IMSI_MASK AS IMSI_MASK,
      DECODE (LENGTH (TRIM (TRANSLATE (ig.IMSI_MASK, '$?', ' '))), NULL, 0, LENGTH (TRIM (TRANSLATE (ig.IMSI_MASK, '$?', ' ')))) IMSI_PREFIX_LENGTH,
      SUBSTR (TRANSLATE (ig.IMSI_MASK, '?$ 0123456789', 'ab'), 1, 1) IMSI_SUFIX_TYPE,
      DECODE (LENGTH (TRANSLATE (ig.IMSI_MASK, '?$ 0123456789', 'ab')), NULL, 0, LENGTH (TRANSLATE (ig.IMSI_MASK, '?$ 0123456789', 'ab'))) IMSI_SUFIX_LENGTH,
      mg.MSISDN_MASK AS MSISDN_MASK,
      DECODE (LENGTH (TRIM (TRANSLATE (mg.MSISDN_MASK, '$?', ' '))), NULL, 0, LENGTH (TRIM (TRANSLATE (mg.MSISDN_MASK, '$?', ' ')))) MSISDN_PREFIX_LENGTH,
      SUBSTR (TRANSLATE (mg.MSISDN_MASK, '?$ 0123456789', 'ab'), 1, 1) MSISDN_SUFIX_TYPE,
      DECODE (LENGTH (TRANSLATE (mg.MSISDN_MASK, '?$ 0123456789', 'ab')), NULL, 0, LENGTH (TRANSLATE (mg.MSISDN_MASK, '?$ 0123456789', 'ab'))) MSISDN_SUFIX_LENGTH,
      rn.RN AS RN,
      r.CallingGT AS CallingGT,
      CalledGT AS CalledGT,
      r.OPERATOR AS OPERATOR,
      ct.GT_HLR_GROUP_NAME AS GT_HLR_GROUP_NAME
    FROM 
      GT_HLR_GROUP ghg,
      IMSI_GROUP ig,
      CROSS_TABLE ct,
      ROOTS r,
      ROOT_NUMBER rn,
      MSISDN_GROUP mg
    WHERE     
      ct.GT_HLR_GROUP_NAME = ghg.GT_HLR_GROUP_NAME
      AND ct.IMSI_GROUP_NAME = ig.IMSI_GROUP_NAME
      AND ct.MSISDN_GROUP_NAME=mg.MSISDN_GROUP_NAME
      AND ct.OPERATOR = r.OPERATOR
      AND rn.OPERATOR = ct.OPERATOR
    ORDER BY 
      HLR_PREFIX_LENGTH DESC,
      HLR_SUFIX_TYPE,
      HLR_SUFIX_LENGTH DESC,
      IMSI_PREFIX_LENGTH DESC,
      IMSI_SUFIX_TYPE,
      IMSI_SUFIX_LENGTH DESC,
      MSISDN_PREFIX_LENGTH DESC,
      MSISDN_SUFIX_TYPE,
      MSISDN_SUFIX_LENGTH DESC
)