
var main_cage = document.getElementById("main_cage"),
	loading = document.getElementById("loading"),
	load_more = document.getElementById("load_more"),
	page = 2;

function apply_image(src, has_cr)
{
	src = encodeURIComponent(src);

	let thumb = "/wisata_kaliurang/api.php?action=thumbnail&file="+src,
		cr2 = "/wisata_kaliurang/api.php?action=download&type=cr2&file="+src,
		jpg = "/wisata_kaliurang/api.php?action=download&type=jpg&file="+src;

	main_cage.innerHTML += '<div class="cage">'+
		'<img class="imgd" width="300" height="200" src="'+thumb+'">'+
		'<p class="pp"></p>'+
		(
			has_cr ?
			'<a href="'+cr2+'">'+
				'<button>Download CR2</button>'+
			'</a>&nbsp;' : ''
		) +
		'<a href="'+jpg+'">'+
			'<button>Download JPG</button>'+
		'</a>'+
	'</div>';
}

function load_page(page = 1)
{
	let xhr, j, x;
	xhr = new XMLHttpRequest;
	xhr.onreadystatechange = function () {
		if (this.readyState === 4) {
			loading.style.display = "none";
			j = JSON.parse(this.responseText);
			for (x in j.data) {
				apply_image(j.data[x].name, j.data[x].has_cr);
			}
			if (j.data.length == 0) {
				load_more.style.display = "none";
			}
		}
	};
	xhr.open("GET", "/wisata_kaliurang/api.php?action=fetch&page="+page);
	xhr.send();
}

load_page();

function next_page()
{
	loading.style.display = "";	
	load_page(page++);
}

load_more.addEventListener("click", next_page);
