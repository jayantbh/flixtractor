var exec = require("child_process").exec;
var threadsMax=10,threads=0,incr=0;
function threader(){
	if(threads<threadsMax){
		threads++;
		console.log('Threads running: '+threads+'.');
		incr+=20
		exec('php flipkart.php '+incr,function(err,data){
			if (err) {
				console.log('Error: '+err);
			}
			else{
				console.log('Data entered. '+data+'\n');
			}
			threads--;
			console.log('Threads running: '+threads+'.');
			threader()
		});
		threader()
	}
}
console.log('Flipkart Extraction Begin.\n');
threader();