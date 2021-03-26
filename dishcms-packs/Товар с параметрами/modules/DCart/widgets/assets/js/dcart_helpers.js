/**
 * DCart helpers 
 */

/**
 * Проверка, что переменная является объектом
 * @param mixed variable проверяемая переменная.
 * @return boolean
 */
function dcart_is_object(variable) {
	return (typeof(variable) == 'object'); 
}

/**
 * Проверка, что переменная является строкой
 * @param mixed variable проверяемая переменная.
 * @return boolean
 */
function dcart_is_string(variable) {
	return (typeof(variable) == 'string'); 
}

/**
 * Проверка, что переменная определена
 * @param mixed variable проверяемая переменная.
 * @return boolean
 */
function dcart_is_defined(variable) {
	return (typeof(variable) != 'undefined'); 
}
