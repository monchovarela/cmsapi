/**
 *  Search in table
 * 
 * @param  {string}
 * @return {array}
 */
const searchPages = name => {
    var input, filter, table, tr, td, i;
    input = document.getElementById("myInput");
    filter = input.value;
    if (filter !== '') {
        axios.get(site_url + '/' + name + '/search/' + filter)
            .then(r => {
                let html = '';
                Array.from(r.data.content).forEach(item => {
                    html += `<a class="list-group-item" href="${site_url}/${name}/edit/${item.uid}" style="text-decoration:none;">
                  <span class="text-primary">${item.name}</span> - ${item.title} - ${item.created}
                </a>`;
                });
                document.getElementById("mySearch").innerHTML = html;
            });
    } else {
        document.getElementById("mySearch").innerHTML = '';
    }
}

