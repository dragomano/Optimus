(function(mod) {
	mod(CodeMirror);
})(function(CodeMirror) {
	"use strict";

	CodeMirror.defineMode("apache", function () {
		return {
			token: function (stream, state) {
				var sol = stream.sol() || state.afterSection;
				var eol = stream.eol();

				state.afterSection = false;

				if (sol) {
					if (state.nextMultiline) {
						state.inMultiline = true;
						state.nextMultiline = false;
					} else {
						state.position = "def";
					}
				}

				if (eol && !state.nextMultiline) {
					state.inMultiline = false;
					state.position = "def";
				}

				if (sol) {
					while (stream.eatSpace()) {}
				}

				var ch = stream.next();

				if (sol && (ch === "#")) {
					state.position = "comment";
					stream.skipToEnd();
					return "comment";
				} else if (ch === " ") {
					state.position = "variable";
					//return null;
				} else if (ch === '"') {
					if (stream.skipTo('"')) { // Quote found on this line
						stream.next();          // Skip quote
					} else {
						stream.skipToEnd();    // Rest of line is string
					}
					state.position = "quote";

				}

				return state.position;
			},

			startState: function () {
				return {
					position: "def", // Current position, "def", "quote" or "comment"
					nextMultiline: false, // Is the next line multiline value
					inMultiline: false, // Is the current line a multiline value
					afterSection: false // Did we just open a section
				};
			}

		};
	});

	CodeMirror.defineMIME("application/x-extension-htaccess", "apache");
});