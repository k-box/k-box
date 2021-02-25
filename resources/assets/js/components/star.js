// function exposed to star-button.blade.php through evolution-components.js
export default function(data) {

    return {
        error: false,
        starred: data.starID ? true : false, 
		starID: data.starID || null,
		inProgress: false,
		documentID: data.documentID,
		count: data.count || null,
        
        star () {
			//if starred: remove star (and set starred to false), else add it and set starred to true
			this.inProgress = true; //starring in progress

			if(this.starred) {
				DMS.Services.Starred.remove(this.starID, function() {
					
					this.starred = false;					//set starred to false
					this.starID = null;					//set starID to null
					if(this.count){
						this.count = this.count - 1;
					}
					
					setTimeout(function(){ 
						this.inProgress = false; //starring done
					}.bind(this), 1000);
				}.bind(this), 
				function(jqXHR, textStatus, errorThrown){
					this.error = true;
					console.error('error on starred-remove response', textStatus, errorThrown);
				}.bind(this));
				
			}else { //add star to the file
				
				DMS.Services.Starred.add({descriptor: this.documentID, visibility:'private'}, function(data){
					
					if(data.id) { //if starred successful, assign starID to the value
						this.starred = true;				//set starred to true
						this.starID = data.id;		//set starID	
						
						setTimeout(function(){
							this.inProgress = false; //starring done
						}.bind(this), 1000);
					}
				}.bind(this), function(jqXHR, textStatus, errorThrown){
					this.error = true;
					console.error('error on starred-add response', textStatus, errorThrown);
				}.bind(this));
			}
        },
        
    }
}