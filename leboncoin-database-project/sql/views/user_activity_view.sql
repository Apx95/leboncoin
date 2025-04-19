CREATE VIEW user_activity_view AS
SELECT 
    u.user_id,
    u.pseudo,
    COUNT(DISTINCT a.ad_id) AS total_ads,
    COUNT(DISTINCT m.message_id) AS total_messages,
    MAX(a.created_at) AS last_ad_date,
    MAX(m.timestamp) AS last_message_date
FROM 
    users u
LEFT JOIN 
    ads a ON u.user_id = a.user_id
LEFT JOIN 
    messages m ON u.user_id = m.sender_id OR u.user_id = m.receiver_id
GROUP BY 
    u.user_id, u.pseudo;