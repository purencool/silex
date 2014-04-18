toDo = {
    models: {},
    collections: {},
    views: {}
};

//-- todo model is the intitial toDoNote object
toDo.models.toDoNote = Backbone.Model.extend({
    defaults: {
        'id': {
            'date': '',
            'client': '',
            'note': '',
            'tags': ''
        }
    }
});

//-- create collections
toDo.collections.toDoNotes = Backbone.Collection.extend({
    initialize: function() {
        this.on('all', function(e) {
            console.log(JSON.stringify(this, null, 2) + " event: " + e);
        });
    },
    model: toDo.models.toDoNote, // creates model
    url: "/todo-json"
});

toDo.views.toDoNoteView = Backbone.View.extend({
  initialize : function() {
    console.log("This is  the view");
     this.render();
  },
  render:function() {
    this.$el.html("<div>Testing to see if this will work</div>");
    return this;
  }   
});
    var toDoNotes = new toDo.collections.toDoNotes;
    toDoNotes.fetch();
    var sx = new toDo.views.toDoNoteView;
