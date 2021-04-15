import {render} from 'react-dom';
import React, {useEffect, useState, useRef, useCallback} from 'react';
import {usePaginatedFetch} from './hooks';
import {useFetch} from './hooks';
import {Field} from '../components/Form.jsx';

function Comments({post, user}){ 
    console.log(post)
    const {items: comments, load, loading, count, hasMore} = usePaginatedFetch('/api/comments?article='+ post)

    useEffect(() => { 
        load()
    },[])

    return <div>
                <Title count={count} />
                {comments.map(c => <Comment key={c.id} comment={c} />)}
                {hasMore && <button disabled={loading} className='btn btn-primary'onClick={load}>Charger plus de commentaires</button>}
                {user && <CommentForm post={post} user={user} />}
            </div>
}

const dateFormat = {
    dateStyle:'medium', 
    timeStyle:'short'
}

const Comment = React.memo(({comment}) => { 
    const date = new Date(comment.createdAt);
    return <div className="row">
        <h6 className="col-sm-3">
            <strong>{comment.author}</strong>
            <br/>commenté le <br/>
            <strong>{date.toLocaleString(undefined, dateFormat)}</strong>
        </h6>
        <div className="col-sm-9"><p><div dangerouslySetInnerHTML={{__html: comment.content}} /></p></div>
    </div>
})

const CommentForm = React.memo(({ post, user, username }) => {
    const ref = useRef(null)

    const {load, loading, errors, clearError} = useFetch('/api/comments', 'POST')
    const onSubmit = useCallback(e => {
        console.log(ref.current.value);
        console.log(post);
        e.preventDefault()
        console.log(username)
        load({
            "content": ref.current.value,
            "article": "/api/articles/" + post,
            "createdAt" : new Date()
        })
    },[load, ref, post])

    return <div className="well">
            <form onSubmit={onSubmit}>
                <fieldset>
                    <legend>Laisser un commentaire</legend>
                </fieldset>
                <Field name='content' help="Les commentaires non conformes à notre code de conduite seront modérés." ref={ref}
                error={errors['content']} onChange={clearError.bind(this, 'content')}>Votre commentaire </Field>
                <div className="form-group">
                        <button disabled={loading} className='btn btn-success'>
                            Envoyer
                        </button>
                </div>
            </form>
    </div>
})

function Title({count}){ 
    return  <div>
                <h4>{count} Commentaire{count > 1 ? 's' : ''}</h4>
            </div>
}

class CommentsElement extends HTMLElement{
    connectedCallback() {
        const post = parseInt(this.dataset.post, 10);
        const username = this.dataset.username;
        const user = parseInt(this.dataset.user, 10) || null ;
        render(<Comments username={username} user={user} post={post} />, this)
    }

}
customElements.define('post-comments', CommentsElement)