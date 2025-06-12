(function() {
    'use strict';

    // const url = window.location.pathname.replace("/", ""); // Original, naive URL parsing
    // const apiURl = `https://api-indianime.herokuapp.com`; // Hardcoded API, should be configured

    // It seems the functionality of this script is largely replaced by server-side rendering with Twig.
    // Commenting out automatic execution to prevent conflicts and unnecessary API calls.
    // If specific client-side interactions from this script are still needed, they should be invoked explicitly
    // or refactored into smaller, more targeted modules/functions.

    /*
    function loadAnimeDetails() {
        const pathSegments = window.location.pathname.split('/').filter(Boolean); // More robust
        const url = pathSegments.pop(); // Get the last segment, assumed to be anime identifier
        const apiURl = (typeof GOGO_API_URL !== 'undefined') ? GOGO_API_URL : 'https://api-indianime.herokuapp.com'; // Example: use a global config

        if (!url) {
            console.error("Anime identifier not found in URL for loadAnimeDetails.");
            return;
        }

        let apiUrlAnimeDetails = `${apiURl}/getAnime/${url}`;

        async function loadAnime() {
            try {
                const response = await fetch(apiUrlAnimeDetails);
                if (!response.ok) {
                    console.error("Failed to fetch anime details:", response.status, response.statusText);
                    return;
                }
                const anime = await response.json();
                //console.log(anime)

                function loadDetail() {
                    let name = anime['name'];
                    const animeName = document.getElementById('animeName');
                    if (animeName) animeName.innerHTML = name;

                    const animeImg = document.getElementById('animeImg');
                    if (animeImg) animeImg.setAttribute('src', `${anime['img_url']}`);

                    const animeInfo = document.getElementById('animeInfo');
                    if (animeInfo) animeInfo.innerHTML = `<span>Plot Summary: </span>${(anime['about'] || '').replace('Plot Summary:', "")}`;

                    const animeReleased = document.getElementById('animeReleased');
                    if (animeReleased) animeReleased.innerHTML = `<span>Released: </span>${(anime['released'] || '').replace('Released:', "")}`;

                    const animeOtherName = document.getElementById('animeOtherName');
                    if (animeOtherName) animeOtherName.innerHTML = `<span>Other name: </span>${(anime['othername'] || '').replace("Other name", "")}`;

                    function animeStatus() {
                        let statusUrl;
                        let title;
                        const status = (anime['status'] || '').replace('Status:', "").trim();
                        // console.log(status); // Debug

                        if (status.toLowerCase() === 'completed') { // More robust comparison
                            statusUrl = '/status/completed'; // Assuming this path exists
                            title = 'Completed';
                        } else {
                            statusUrl = '/status/ongoing'; // Assuming this path exists
                            title = 'Ongoing';
                        }
                        const animeStatusElem = document.getElementById('animeStatus');
                        if (animeStatusElem) animeStatusElem.innerHTML = `<span>Status: </span>
                        <a href="${statusUrl}" title="${title} Anime">${status}</a>`;
                    }
                    animeStatus();

                    const genre = document.getElementById('genre');
                    if (genre) genre.innerHTML = `<span>Genre: </span>${(anime['genre'] || '').replace('Genre: ', '')}`;

                    const h2title = document.getElementById('h2title');
                    if (h2title) h2title.innerHTML = `${anime['name']}`;
                }
                loadDetail();

                function loadEpisode() {
                    const episode_related = document.getElementById('episode_related');
                    if (!episode_related) return;

                    let episode = anime['episode_id'];
                    if (!Array.isArray(episode)) return;

                    let episodeHTML = "";
                    let episodeContent;
                    episode.forEach(function (element, index) {
                        episodeContent = `
                            <li>
                              <a href="${element}">
                                <div class="name"><span>EP</span> ${index + 1}</div>
                                <div class="vien"></div>
                                <div class="cate">SUB</div>
                              </a>
                            </li>`;
                        episodeHTML += episodeContent;
                    });
                    episode_related.innerHTML = episodeHTML;
                }
                loadEpisode();
            } catch (error) {
                console.error("Error in loadAnime:", error);
            }
        }
        loadAnime();
    }
    // loadAnimeDetails(); // Deactivated: Functionality moved to server-side with Twig

    function loadRecentRelease() {
        const apiURl = (typeof GOGO_API_URL !== 'undefined') ? GOGO_API_URL : 'https://api-indianime.herokuapp.com'; // Example
        async function loadRecent() {
            try {
                const apiUrlRecentReleases = `${apiURl}/getRecent/1`; // Assuming page 1 for this block
                const response = await fetch(apiUrlRecentReleases);
                 if (!response.ok) {
                    console.error("Failed to fetch recent releases:", response.status, response.statusText);
                    return;
                }
                const recentReleases = await response.json();
                //console.log(recentReleases);
                const recentEpisodesContainer = document.getElementById('recentEpisodes'); // Make sure this ID exists in relevant Twig templates
                if (!recentEpisodesContainer) return;

                let recentEpisodesHTML = "";
                let recentEpisodesContent;

                if (Array.isArray(recentReleases)) {
                    recentReleases.forEach(function (element) {
                        recentEpisodesContent = `
                        <li>
                         <a href="${element['r_anime_id']}"
                          title="${element['r_name']}">
                          <div class="thumbnail-recent"
                            style="background: url('${element['r_img_url']}');">
                          </div>
                          ${element['r_name']}
                         </a>
                         <a href="${element['r_anime_id']}"
                          title="${element['r_name']}">
                          <p class="time_2">${element['episode_num']}</p>
                         </a>
                        </li>
                        `;
                        recentEpisodesHTML += recentEpisodesContent;
                    });
                    recentEpisodesContainer.innerHTML = recentEpisodesHTML;
                }
            } catch (error) {
                console.error("Error in loadRecent:", error);
            }
        }
        loadRecent();
    }
    // loadRecentRelease(); // Deactivated: Functionality moved to server-side (php/include/recentRelease.php)

    function loadDisqus(){
        const disqusContainer = document.getElementById('loadDisqus');
        if (!disqusContainer) return;

        // Attempt to make page URL and identifier dynamic
        var disqus_config = function () {
            this.page.url = window.location.href;  // Dynamic URL
            // this.page.identifier = window.location.pathname; // Example identifier, might need adjustment
        };

        // Standard Disqus embed code
        (function () {  // DON'T EDIT BELOW THIS LINE
            var d = document, s = d.createElement('script');
            // Use a variable for shortname, passed from server or a global JS config
            var disqus_shortname = (typeof DISQUS_SHORTNAME !== 'undefined') ? DISQUS_SHORTNAME : 'gogoanimetv'; // Fallback
            s.src = 'https://' + disqus_shortname + '.disqus.com/embed.js';
            s.setAttribute('data-timestamp', +new Date());
            (d.head || d.body).appendChild(s);
        })();

        // No need for innerHTML manipulation for the script itself, Disqus handles it.
        // The <noscript> part should be in the Twig template where Disqus is meant to appear.
    }
    // loadDisqus(); // Deactivated for now. If used, ensure DISQUS_SHORTNAME is globally available or passed.
    // And that the <div id="disqus_thread"></div> is present in the Twig template.
    */

})();