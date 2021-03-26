function utf8_decode ( str_data ) {	// Converts a string with ISO-8859-1 characters encoded with UTF-8   to single-byte
	// ISO-8859-1
	// 
	// +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
	if(typeof str_data != 'string') str_data='';
	var string = "", i = 0, c = c1 = c2 = 0;

	while ( i < str_data.length ) {
		c = str_data.charCodeAt(i);
		if (c < 128) {
			string += String.fromCharCode(c);
			i++;
		} else if((c > 191) && (c < 224)) {
			c2 = str_data.charCodeAt(i+1);
			string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
			i += 2;
		} else {
			c2 = str_data.charCodeAt(i+1);
			c3 = str_data.charCodeAt(i+2);
			string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
			i += 3;
		}
	}

	return string;
}
