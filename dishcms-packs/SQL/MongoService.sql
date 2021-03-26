// просмотр предложений
SELECT p.product_id, p.id, i.name, REPLACE(i.name, 'Вентилятор ВК', 'Круглый канальный вентилятор ВК')
from shop_product_variants as p 
left join shop_product_variants_i18n as i using(id)
where p.product_id in (select product_id from shop_product_categories where category_id=3046)

// предложения
UPDATE shop_product_variants_i18n 
SET name=REPLACE(name, 'Вентилятор ВК', 'Круглый канальный вентилятор ВК')
WHERE id IN (SELECT id FROM shop_product_variants WHERE product_id IN (SELECT product_id FROM shop_product_categories WHERE category_id=3046))

// товары
UPDATE shop_products_i18n
SET name=REPLACE(name, 'Вентилятор ВК', 'Круглый канальный вентилятор ВК')
WHERE id IN (SELECT product_id FROM shop_product_categories WHERE category_id=3046)
