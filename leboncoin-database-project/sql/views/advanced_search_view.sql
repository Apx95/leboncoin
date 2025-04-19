CREATE VIEW advanced_search AS
SELECT 
    a.ad_id,
    a.title,
    a.description,
    a.price,
    a.status,
    a.location,
    a.created_at,
    u.user_id,
    u.pseudo,
    u.email,
    c.category_name
FROM 
    ads a
JOIN 
    users u ON a.user_id = u.user_id
JOIN 
    categories c ON a.category_id = c.category_id
WHERE 
    a.status = 'active';