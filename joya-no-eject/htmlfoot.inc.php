
	</div>
	<div id="footer">
		<a href="/"><img src="https://eject.kokuda.org/wp-content/uploads/2013/09/title.png" style="width:200px;"><br>
		Ejectコマンドユーザー会</a>
	</div>
	<script src="https://mastoshare.net/js/button.js"></script>
	<script type="text/javascript" src="https://s.hatena.ne.jp/js/HatenaStar.js"></script>
	<script type="text/javascript">
Hatena.Star.SiteConfig = {
    entryNodes: {
        '#star': {
            uri: '#star a',
            title: 'document.title',
            container: 'parent'
        }
    }
};

async function chkcount() {
    return fetch('count.dat',{cache: 'no-cache'}).then(function(r){ return r.text(); }).then(function(c){ return c; });
}
function update_count(c) {
    var t = document.getElementById('count');
    if (c < 0) {
	t.innerHTML = '';
    } else if (c == 0) {
        t.innerHTML = 'いまのところ、まだだれも鐘をついていないようです';
    } else {
        t.innerHTML = 'いまのところ、' + c + '人が鐘をつきました！';
    }
    setTimeout(async function() {
        c = await chkcount();
        update_count(c);
    }, 10000);
}
window.onload = async function() {
    c = await chkcount();
    update_count(c);
};
	</script>
</div>
</body>
</html>

