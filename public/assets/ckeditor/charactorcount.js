$(document).ready(function(){

   var editAbstract=CKEDITOR.instances.englishdescription;

   editAbstract.on("key",function(e) {      
                           
      var maxLength=e.editor.config.maxlength;
         
      e.editor.document.on("keyup",function() {KeyUp(e.editor,maxLength,"letterCount");});
      e.editor.document.on("paste",function() {KeyUp(e.editor,maxLength,"letterCount");});
      e.editor.document.on("blur",function() {KeyUp(e.editor,maxLength,"letterCount");});
   },editAbstract.element.$);
	 var editAbstract1=CKEDITOR.instances.arabicdescription;
   editAbstract1.on("key",function(e) {      
                           
      var maxLength=e.editor.config.maxlength;
         
      e.editor.document.on("keyup",function() {KeyUp(e.editor,maxLength,"letterCount");});
      e.editor.document.on("paste",function() {KeyUp(e.editor,maxLength,"letterCount");});
      e.editor.document.on("blur",function() {KeyUp(e.editor,maxLength,"letterCount");});
   },editAbstract1.element.$);
       var editAbstract2=CKEDITOR.instances.englishdescription1;
   editAbstract2.on("key",function(e) {      
                           
      var maxLength=e.editor.config.maxlength;
         
      e.editor.document.on("keyup",function() {KeyUp(e.editor,maxLength,"letterCount");});
      e.editor.document.on("paste",function() {KeyUp(e.editor,maxLength,"letterCount");});
      e.editor.document.on("blur",function() {KeyUp(e.editor,maxLength,"letterCount");});
   },editAbstract2.element.$);

    var editAbstract3=CKEDITOR.instances.arabicdescription1;
   editAbstract3.on("key",function(e) {      
                           
      var maxLength=e.editor.config.maxlength;
         
      e.editor.document.on("keyup",function() {KeyUp(e.editor,maxLength,"letterCount");});
      e.editor.document.on("paste",function() {KeyUp(e.editor,maxLength,"letterCount");});
      e.editor.document.on("blur",function() {KeyUp(e.editor,maxLength,"letterCount");});
   },editAbstract3.element.$);
   var editAbstract4=CKEDITOR.instances.englishdescription2;
   editAbstract4.on("key",function(e) {      
                           
      var maxLength=e.editor.config.maxlength;
         
      e.editor.document.on("keyup",function() {KeyUp(e.editor,maxLength,"letterCount");});
      e.editor.document.on("paste",function() {KeyUp(e.editor,maxLength,"letterCount");});
      e.editor.document.on("blur",function() {KeyUp(e.editor,maxLength,"letterCount");});
   },editAbstract4.element.$);

    var editAbstract5=CKEDITOR.instances.arabicdescription2;
   editAbstract5.on("key",function(e) {      
                           
      var maxLength=e.editor.config.maxlength;
         
      e.editor.document.on("keyup",function() {KeyUp(e.editor,maxLength,"letterCount");});
      e.editor.document.on("paste",function() {KeyUp(e.editor,maxLength,"letterCount");});
      e.editor.document.on("blur",function() {KeyUp(e.editor,maxLength,"letterCount");});
   },editAbstract5.element.$);
   //function to handle the count check
   function KeyUp(editorID,maxLimit,infoID) {

      //If you want it to count all html code then just remove everything from and after '.replace...'
      var text=editorID.getData().replace(/<("[^"]*"|'[^']*'|[^'">])*>/gi, '').replace(/^\s+|\s+$/g, '');
      $("#"+infoID).text(text.length);

      if(text.length>maxLimit) {   
         alert("You cannot have more than "+maxLimit+" characters"); 
		    
    // Create and show the notification.
							
				// Use shortcut - it has the same result as above.
				//var notification2 = editor.showNotification( 'Error occurred', 'warning' );
			
         editorID.setData(text.substr(0,maxLimit));
         editor.cancel();
		 editorID.preventDefault();
		 return false;
      } else if (text.length==maxLimit-1) {
         //alert("WARNING:\nYou are one character away from your limit.\nIf you continue you could lose any formatting");
         editor.cancel();
      }
   }   
   
});