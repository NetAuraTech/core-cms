<div class="hstack">
    @if(social('rss') != '')
        <a href="{{ social('rss') }}" title="Flux RSS" target="_blank" rel="noreferrer">
            <img src="/images/social/rss.svg" width="24" height="24" alt="" loading="lazy">
        </a>
    @endif
    @if(social('github') != '')
        <a href="{{ social('github') }}" title="Github" target="_blank" rel="noreferrer">
            <img src="/images/social/github.svg" width="24" height="24" class="lighten-dark" alt="" loading="lazy">
        </a>
    @endif
    @if(social('youtube') != '')
        <a href="{{ social('youtube') }}" title="Youtube" target="_blank" rel="noreferrer">
            <img src="/images/social/youtube.svg" width="24" height="24" alt="" loading="lazy">
        </a>
    @endif
    @if(social('facebook') != '')
        <a href="{{ social('facebook') }}" title="Facebook" target="_blank" rel="noreferrer">
            <img src="/images/social/facebook.svg" width="24" height="24" alt="" loading="lazy">
        </a>
    @endif
    @if(social('twitter') != '')
        <a href="{{ social('twitter') }}" title="Twitter" target="_blank" rel="noreferrer">
            <img src="/images/social/twitter.svg" width="24" height="24" alt="" loading="lazy">
        </a>
    @endif
    @if(social('instagram') != '')
        <a href="{{ social('instagram') }}" title="Instagram" target="_blank" rel="noreferrer">
            <img src="/images/social/instagram.svg" width="24" height="24" alt="" loading="lazy">
        </a>
    @endif
    @if(social('linkedin') != '')
        <a href="{{ social('linkedin') }}" title="Linkedin" target="_blank" rel="noreferrer">
            <img src="/images/social/linkedin.svg" width="24" height="24" alt="" loading="lazy">
        </a>
    @endif
    @if(social('twitch') != '')
        <a href="{{ social('twitch') }}" title="Twitch" target="_blank" rel="noreferrer">
            <img src="/images/social/twitch.svg" width="24" height="24" alt="" loading="lazy">
        </a>
    @endif
</div>
