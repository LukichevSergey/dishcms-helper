Для v.1.01:
1. Добавить класс стиля
.shop-menu .expanded > UL {
	display: block !important;
}
2. поправить класс стиля
.shop-menu > li > ul  {
    list-style: none;
    list-style-type: none;
    padding: 0;
    margin-top: 3px;
    display: none; <--- это добавить
}
