try {
	throw "Error";
} catch(e) {
	logger.log(e.toString());
} finally {
	alert("Done");
}