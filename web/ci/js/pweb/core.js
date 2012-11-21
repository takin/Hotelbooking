// This file adds functionality to core Javascript types
// This file is a primary dependancy for any pweb library
// Author:   Louis-Michel Raynauld
// Creation: 7 March 2012
// Version: 0.1

//When you make a new object, you can select the object that should be its
//prototype. The mechanism that JavaScript provides to do this is messy and complex,
//but it can be significantly simplified. We will add a create method to the Object
//function. The beget method creates a new object
//that uses an old object as its prototype. 

//This function is part of JS 1.8.5 but added here for backward compatibility

//Make everything in a container
var PWEB = {
	version: 0.1,
//	create: function (o) {
//	         var F = function () {};
//	         F.prototype = o;
//	         return new F();
//	}
};

