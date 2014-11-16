toDo.views.toDoNoteView = Backbone.View.extend({
    initialize: function() {
        this.toDoNotes = new toDo.collections.toDoNotes();
        this.listenTo(this.toDoNotes, 'add', this.render);
        this.toDoNotes.fetch();
    },
    render: function() {
        var json_obj = this.toDoNotes.models[0].attributes;
        var output = "<ul>";
        $.each(json_obj, function(keyRoot, valRootObj) { 
          if(keyRoot !== 'id') {
            var resultHtml = "";
            var editHtml = "";
            $.each(valRootObj, function(key, val) {  
                resultHtml+="<span class='"+key+"'>"+val+"</span>";
                //-- these are not to be updated
                if(key !== 'date' && key !== 'client') {
                  editHtml += "<div><label>"+key+"</label>\
                    <input id='"+key+"' type='text' value='"+val+"'/>\n\</div>";
                }
            });         
            output += "<li>"+ resultHtml+" <a href='"+keyRoot+"\
             'class='editTodo'>edit</a><div class='hideElement'>"+editHtml+"</div></li>";
            resultHtml='';
          }  
        });
        output += "</ul>";
        //-- hands it off to jquery
        this.$el.html(output);
    },  
    events: {
        "click a.editTodo": "editRow"
    },
    editRow:function(e){
        
        $(this).find('.hideElement').toggle();
        e.preventDefault();
    },
    deleteRow:function(e){
        e.preventDefault();
        console.log('delete row');
    }
});
