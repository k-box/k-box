define("modules/dropdown", ["jquery"], function (_$) {

	var DropMenu = function(element)
	{
		this.trigger = element.find("[data-dropdown-trigger]");
		this.arrow = this.trigger ? this.trigger.find(".arrow") : null;
		this.panel = element.find("[data-dropdown-panel]");
		this.obscurer = element.find("[data-dropdown-obscurer]");
		this.isOpen = false;

		this.open = function(){
			if(this.panel && !this.isOpen){

				this.panel.removeClass('hidden');
				
				if(this.obscurer){
					this.obscurer.removeClass('hidden');
					this.obscurer.on('click', this.close.bind(this));
				}
	
				if(this.arrow){
					this.arrow.addClass("rotate-180");
				}

				this.isOpen = true;
			}
		}
		this.close = function(){
			if(this.panel && this.isOpen) {
				this.panel.addClass('hidden');

				if(this.obscurer){
					this.obscurer.addClass('hidden');
					this.obscurer.off('click', this.close.bind(this));
				}
	
				if(this.arrow){
					this.arrow.removeClass("rotate-180");
				}

				this.isOpen = false;
			}
		};

		this.toggle = function(){
			
			this.isOpen ? this.close() : this.open();

		};

		if(this.trigger){
			this.trigger.on('click', function(evt){
				
				this.toggle();

				return false;
		
			}.bind(this));
		}
	}

	var module = {

		find: function(selector)
		{
			var dropdowns = _$(selector + ' [data-dropdown]');

			if(dropdowns){
				_$.map(dropdowns, function(item){
					return new DropMenu(_$(item));
				});
			}
		}

	};

	return module;
});