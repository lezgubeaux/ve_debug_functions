/* 
Create and manage (id, position, content...) of a debug DIV

in any JS code, use 
 * veDebug->console{
 *      *const message,      // object, array, string, boolean, int
 *      string div id,          // custom ID of a div console
 *      bool newCnt,               // override or append content
 *      bool fixed,             // fix the height of div and show the last output line
 *      int x,                      // viewport left offset
 *      int y,                      // viewport top offset
*/

/* global veDebugDiv */

class DebugLogger {
	// print log message into the DIV
	log(
		message = " ",
		id = "default",
		newCnt = false,
		fixed = true,
		offX = 100,
		offY = 100
	) {
		this.message = message.toString().replace(/[<>]/g, "_"); // Quick sanitization
		this.id = veDebugDiv.divId + this.sanitizeSlug(id);
		this.newCnt = newCnt;
		this.fixed = fixed;
		this.offX = offX;
		this.offY = offY;

		// works for logged admins only
		if (veDebugDiv.logged !== "1") {
			console.log(
				"login as admin user, in order to use custom JS log! The log data appears in the frontend!!!"
			);
		} else {
			this.getDiv();
		}
	}

	// print or use existing DIV
	getDiv() {
		let debugDiv = document.getElementById(this.id);

		if (!debugDiv) debugDiv = this.createDiv(this.id);

		debugDiv.classList.add("ve_debug_div_active");

		const debugCnt = debugDiv.querySelector(".content");
		debugCnt.appendChild(document.createTextNode(this.message));
		debugCnt.appendChild(document.createElement("br"));

		debugDiv.scrollTop = debugDiv.scrollHeight;
		/*         debugDiv.scrollTo({
					top: debugDiv.scrollHeight,
					behavior: "smooth",
				}); */

		return debugDiv;
	}

	// create debug DIV with a given id (sufix)
	createDiv(id) {
		const templ = document.getElementById(veDebugDiv.divId);
		const wrap = templ.parentElement;

		const newDiv = document.createElement("div");
		wrap.appendChild(newDiv);

		newDiv.id = id;
		newDiv.innerHTML = templ.innerHTML;
		// align all debug divs in a row
		this.distributeDivs(id);

		return newDiv;
	}

	// sanitize slug
	sanitizeSlug(slug) {
		// remove non-alphanumeric characters except dashes/underscores
		let sanitized = String(slug).replace(/[^a-zA-Z0-9-_]/g, "");

		// Prepend a letter if the ID starts with a number
		if (/^\d/.test(sanitized)) {
			sanitized = "id_" + sanitized;
		}

		if (sanitized.length < 3) {
			sanitized += "a" + Math.random().toString(36).substring(2, 6);
		}

		return sanitized;
	}

	// get order number of curren deBug div
	distributeDivs(id) {
		// console.log(`div[id^=${veDebugDiv.divId}]`);
		document
			.querySelectorAll(`div[id^=${veDebugDiv.divId}]`)
			.forEach((div, index) => {
				if (div.id === id) {
					// reposition only the current (}not to repeat dom element manipulation)
					div.style.left = (index - 1) * 320 + 20 + "px";
				}
			});
	}
}

window.veDebug = new DebugLogger();
