import {render} from 'react-dom';
import React from 'react';

function Comments(){ 
    return <div>bonjour tout le monde</div>
}

class CommentsElement extends HTMLElement{

    connectedCallback() {
        render(<Comments />, this)
    }

}
customElements.define('post-comments', CommentsElement)