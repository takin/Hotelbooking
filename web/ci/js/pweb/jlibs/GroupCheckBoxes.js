// Function GroupCheckBoxes() is a class constructor for the implementation of a checkbox group widget. 
// GroupCheckBoxes() requires an unordered list structure, with the first list entry being the group 
// checkbox and the remaining entries being the checkboxes controlled by the group. Each list entry 
// must contain an image tag that will be used to display the state of the checkbox. 
// 
// @param(list string) list is the id of the unordered list that GroupCheckBoxes is to be bound to 
// 
// @return N/A 
// 
function GroupCheckBoxes(list,first_all) { 

  // define object properties 
  this.$id = $('#' + list); 

  this.unchecked = 0; 
  this.checked = 1; 
  this.mixed = 2; 
  
  //function invoked after a click on any checkbox of the group
  this.clickaction = function (){};

  //No checkbox with a all behavior by default
  this.$checkall_li    = false;
  this.isFirstCheckAll = false;
  
  if(first_all === true)
  {
	this.isFirstCheckAll = true;
	  
	//LI element containing the check all checkbox
	this.$checkall_li    = this.$id.find('li').first();
	  
	//LI elements containing all the checkboxes
    this.$checkboxes_li = this.$checkall_li.siblings(); 
  }
  else
  {
	//LI elements containing all the checkboxes
	this.$checkboxes_li = this.$id.find('li');
  }
  //Nnumber of checkboxes that are checked
  this.checkedCount = 0;  

  // initialize the GroupCheckBoxes object 
  this.init(); 

  // bind event handlers 
  this.bindHandlers(); 

} // end GroupCheckBoxes() constructor 

// Function init() is a member function to initialize the GroupCheckBoxes object. Initial checkbox 
// states are set according to the aria-checked property of the checkboxes in the group. 
// 
// return N/A 
// 
GroupCheckBoxes.prototype.init = function() { 

  var that = this; 

  this.$checkboxes_li.each(function() { 
    if ($(this).find('input').attr('checked') === true) { 
      that.adjCheckedCount(true); 
    } 
  }); 

}; // end init() 

GroupCheckBoxes.prototype.clickAction = function(f) { 
  this.clickaction = f;
}; // end clickAction() 

//@return checked values in array
GroupCheckBoxes.prototype.getCheckedValues = function() { 
  var that = this,
      values = []; 

  this.$checkboxes_li.each(function() { 
    var inputcheck = $(this).find('input');
    if (inputcheck.attr('checked') === true) { 
      values.push( inputcheck.attr('value'));
    } 
  }); 

  return values;
}; // end getCheckedValues() 

// 
// Function bindHandlers() is a member function to bind event handlers to the checkboxes in the 
// checkbox group. 
// 
// @return N/A 
// 
GroupCheckBoxes.prototype.bindHandlers = function() { 

  var that = this; 

  /////////// Bind groupbox handlers //////////////// 
  // bind a click handler 
  if(this.$checkall_li !== false)
  {
	  this.$checkall_li.find('input').click(function(e) {
	    var ret = that.handleGroupboxClick($(this), e);
	    that.clickaction(); 
	    return ret; 
	  });
  }

  /////////// Bind checkbox handlers //////////////// 
  this.$checkboxes_li.find('input').click(function(e) { 
    var ret = that.handleCheckboxClick($(this), e);
    that.clickaction();
    return ret; 
  }); 
}; // end bindHandlers() 

// Function setBoxState() is a member function to set a checkbox state. This function sets the 
// aria-checked property to the passed state value and changes the box image to display the new 
// box state. 
// 
// @param($boxID object) $boxID is the jquery object of the checkbox to manipulate 
// 
// @param(state integer) state is the check state to set the box 
// 
// @return N/A 
// 
GroupCheckBoxes.prototype.setBoxState = function($boxID, state) { 

  switch (state) { 
    case this.checked: { 
      $boxID.attr('checked', true); 
      break; 
    } 
    case this.mixed:
    case this.unchecked: { 
      $boxID.attr('checked', false); 
      break; 
    } 
  } // end switch 

}; // end setBoxState() 

// 
// Function adjCheckedCount() is a member function to increment or decrement the count of checked 
// boxes. The function modifies the checkes state of the group box accordingly. 
// 
// @param(inc boolean) inc is true if incrementing the checked count, false if decrementing 
// 
// @return N/A 
// 
GroupCheckBoxes.prototype.adjCheckedCount = function(inc) { 

  // increment or decrement the count 
  if (inc === true) { 
    this.checkedCount++; 
  } 
  else { 
    this.checkedCount--; 
  } 
  
  if(this.$checkall_li !== false)
  {
	  // modify the group box state 
	  if (this.checkedCount === this.$checkboxes_li.length) { 
	    // all the boxes are checked 
	    this.setBoxState(this.$checkall_li.find('input'), this.checked); 
	  } 
	  else if (this.checkedCount > 0) { 
	    // some of the boxes are checked 
	    this.setBoxState(this.$checkall_li.find('input'), this.mixed); 
	  } 
	  else { 
	    // all boxes are unchecked 
	    this.setBoxState(this.$checkall_li.find('input'), this.unchecked); 
	  } 
  }

}; // end adjCheckedCount() 


/////////////////////// Groupbox event handlers ///////////////////////////////// 
GroupCheckBoxes.prototype.handleGroupboxClick = function($id, e) { 
     
  var that = this; 

  switch ($id.attr('checked')) { 
    case true : { 
      // check the group 
      // check all the checkboxes in the group 
      this.$checkboxes_li.each(function() { 
        that.setBoxState($(this).find('input'), that.checked); 
      }); 
      // set the checked count 
      this.checkedCount = this.$checkboxes_li.length; 
      break; 
    } 
    case 'mixed' : 
    case false :
    default : { 
      
      // uncheck the group 
      // clear all the checkboxes in the group 
      this.$checkboxes_li.each(function() { 
        that.setBoxState($(this).find('input'), that.unchecked); 
      }); 

      // reset the checked count 
      this.checkedCount = 0; 
      break; 
    } 
  } // end switch 

  e.stopPropagation(); 
  return true; 
   
}; // end handleGroupboxClick() 
   
/////////////////////// Checkbox event handlers ///////////////////////////////// 

GroupCheckBoxes.prototype.handleCheckboxClick = function($id, e) { 
  if($id.attr('checked') === true) { 
    this.adjCheckedCount(true); 
  } else { 
    this.adjCheckedCount(false); 
  }  // endif 

  e.stopPropagation(); 
  return true; 
   
}; // end handleCheckboxClick() 