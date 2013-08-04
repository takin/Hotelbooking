/*****
created by sasha karpin


********/

function saf_setCookie(c_name,value,exdays)
{
var exdate=new Date();
exdate.setDate(exdate.getDate() + exdays);
var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
document.cookie=c_name + "=" + c_value;
};

function saf_changeCurrency(cur_code){
	site_path = document.location.pathname;

	setCookie('currency_selected', cur_code, 5);
	if ( /\/info\/|^\/[^\/]+$/.test(site_path)){
		
	}else{
        document.location.href = site_path + "?currency="+cur_code;
        
//		document.location.reload();
	}

}