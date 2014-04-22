toDo.views.toDoNoteView = Backbone.View.extend({
    initialize: function() {
        this.toDoNotes = new toDo.collections.toDoNotes();
        this.listenTo(this.toDoNotes, 'add', this.render);
        this.toDoNotes.fetch();


    },
    render: function() {
        var json_obj = this.toDoNotes.models[0].attributes;
        var output = "<ul>";
        for (var i in json_obj)
        {
            var resultHtml ="";
            $.each(json_obj[i], function(key, val) {  
                resultHtml+="<span class='"+key+"'>"+val+"</span>";
            });  
            
            output += "<li>" + resultHtml + "</li>";
            resultHtml='';
        }
        output += "</ul>";

        $('#page').html(output);
    }
});
