(function() {
    'use strict';

    // const url = window.location.pathname.replace("/", ""); // Original, naive URL parsing. Now handled by PHP.
    // const url = "naruto-episode-1"; // Hardcoded test value

    // Base New API URL - This is different from the one now intended via ApiClient
    // const apiURl = "https://animeapi-9qlo.onrender.com";

    // Prxy URL - Proxying should ideally be handled server-side by ApiClient or not at all if direct calls are feasible.
    // const prxyUrl = "https://gogo.druvx13.workers.dev/?u=";

    // Construct the final API URL through the prxy
    // const apiUrlEpisodeDetail = `${prxyUrl}${encodeURIComponent(`${apiURl}/getEpisode/${url}`)}`;

    // The functionality of this script (fetching episode details, anime details, recent releases,
    // and updating the DOM including meta tags) is now largely handled by server-side rendering
    // via streaming.php, ApiClient.php, and Twig templates.
    // Running this script as-is would conflict with server-rendered content and make redundant API calls
    // to a different API source (api-indianime.herokuapp.com vs the one configured in ApiClient).

    // Meta tags should be rendered server-side for SEO.
    // Video player iframe source and episode lists are now rendered server-side.

    // Therefore, the automatic execution of its functions is commented out.
    // If any specific client-side UI interactions from this script are still uniquely required
    // (beyond what main.js or other global scripts provide, e.g. for video server switching),
    // those specific parts would need to be identified and carefully reintegrated.
    // For now, neutralizing the script is the safest approach.

    /*
    function loadEpisodeDetail() {
        // ... (original function content) ...
    }

    // loadEpisodeDetail(); // Deactivated

    function loadRecentRelease() {
        // ... (original function content) ...
    }
    // loadRecentRelease(); // Deactivated
    */

    // Note: Disqus loading is typically handled by a script tag in the main HTML/Twig template.
    // If this specific loadDisqus() function was intended for a dynamic load button or something,
    // it would need to be re-evaluated in context of the Twig structure.
    // The DISQUS_SHORTNAME is now passed from PHP to streaming.html.twig.

})();
