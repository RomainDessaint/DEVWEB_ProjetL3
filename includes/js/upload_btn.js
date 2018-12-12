var rules = {
a:"àáâãäå",
A:"ÀÁÂ",
e:"èéêë",
E:"ÈÉÊË",
i:"ìíîï",
I:"ÌÍÎÏ",
o:"òóôõöø",
O:"ÒÓÔÕÖØ",
u:"ùúûü",
U:"ÙÚÛÜ",
y:"ÿ",
c:"ç",
C:"Ç",
n:"ñ",
N:"Ñ"
};

function getJSONKey(key) {
	for (acc in rules) {
		if (rules[acc].indexOf(key)>-1){return acc}
	}
}

function replaceSpec(Texte) {
	regstring=""
	for (acc in rules) {
		regstring+=rules[acc]
	}
	reg=new RegExp("["+regstring+"]","g" )
	return Texte.replace(reg,function(t) { return getJSONKey(t) });
}

const realFileBtn = document.getElementById("real_button");
const fakeFileBtn = document.getElementById("fake_button");
const fakeFileTxt = document.getElementById("fake_text");

fakeFileBtn.addEventListener("click", function() {
	realFileBtn.click();
});

realFileBtn.addEventListener("change", function() {
	if(realFileBtn.value) {
		var TestTexte = realFileBtn.value;
		console.log(TestTexte);
		fakeFileTxt.innerHTML = replaceSpec(TestTexte).match(/[\/\\]([\w\d\s\.\-\(\)]+)$/)[1];
	} else {
		fakeFileTxt.innerHTML = "Aucun fichier choisi.";
	}
});
