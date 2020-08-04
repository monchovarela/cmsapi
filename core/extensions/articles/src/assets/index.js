/**
 *  Search in table
 *
 * @param  {string}
 * @return {array}
 */
const searchArticles = name => {
    var input, filter, table, tr, td, i;
    input = document.getElementById("myInput");
    filter = input.value;
    if (filter !== '') {
        axios.get(site_url + '/' + name + '/search/' + filter)
            .then(r => {
                let html = '';
                Array.from(r.data.content).forEach(item => {
                    html += `<li class="padding small white border hover-light-grey margin round">
                <a href="${site_url}/${name}/edit/${item.uid}" style="text-decoration:none;">
                  <span class="text-blue">${item.name}</span> - ${item.title} - ${item.created}
                </a>
              </li>`;
                });
                document.getElementById("mySearch").innerHTML = html;
            });
    } else {
        document.getElementById("mySearch").innerHTML = '';
    }
}

const renderHtml = data => {
    let articleHTML = '';
    data.map(obj => {
        switch (obj.type) {
            case 'paragraph':
                articleHTML += `<p>${obj.data.text}</p>`;
                break;
            case 'header':
                articleHTML += `<h${obj.data.level} class="mb-3 text-black">${obj.data.text}</h${obj.data.level}>`;
                break;
            case 'image':
                let cls = "";
                if (obj.data.withBorder) cls += "border border-dark";
                if (obj.data.stretched) cls += "vw-100";
                if (obj.data.withBackground) cls += " p-5 bg-light ";
                articleHTML += `<figure>
                      <img class="img-fluid ${cls}" src="${obj.data.file.url}" alt="${obj.data.caption}" />
                      <figcaption>${obj.data.caption}</i></figcaption>
                    </figure>`;
                break;
            case 'warning':
                articleHTML += `<div class="mt-3 mb-5 alert alert-info" role="alert">
                    <strong class="font-weight-bold">${obj.data.title}</strong> ${obj.data.message}
                </div>`;
                break;
            case 'list':
                const list = obj.data.items.map(item => {
                    return `<li>${item}</li>`;
                });
                if (obj.data.style === 'unordered') {
                    articleHTML += `<ul class="mt-3 mb-3">${list.join('')}</ul>`;
                } else {
                    articleHTML += `<ol class="mt-3 mb-3">${list.join('')}</ol>`;
                }
                break;
            case 'delimiter':
                articleHTML += `<span class="d-block mt-3 mb-3 w-100 text-center text-black">***</span>`;
                break;
            case 'code':
                articleHTML += `<pre class="text-monospace mt-3 mb-5 w-100 p-3 bg-dark text-light rounded shadow"><code>${obj.data.code}</code></pre>`;
                break;
            case 'quote':
                articleHTML += `<blockquote class="mt-3 mb-5 blockquote text-${obj.data.alignment}"">
                  <p class="mb-0">${obj.data.text}</p>
                  <footer class="blockquote-footer">
                    <cite title="Source Title">${obj.data.caption}</cite>
                  </footer>
                </blockquote>`;
                break;
            case 'table':
                var rows = '';
                obj.data.content.map( function(row) {
                    cells = '';
                    row.map( function (cell) {
                        cells += `<td>${cell}</td>`;
                    });
                    rows += `<tr>${cells}</tr>`;
                });
                articleHTML += `<table class="table table-responsive mt-3 mb-3"><tbody>${rows}</tbody></table>`;
                break;
            case 'embed':
                articleHTML += `<div class="mt-3 mb-3 card text-dark bg-light d-block">
                <div class="card-header">${obj.data.caption}</div>
                    <div class="card-body">
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" src="${obj.data.embed}" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>`;
                break;
        }
    });
    return articleHTML;
}

const base64EncodeUnicode = str =>{
    // First we escape the string using encodeURIComponent to get the UTF-8 encoding of the characters, 
    // then we convert the percent encodings into raw bytes, and finally feed it to btoa() function.
    utf8Bytes = encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function(match, p1) {
            return String.fromCharCode('0x' + p1);
    });

    return btoa(utf8Bytes);
}