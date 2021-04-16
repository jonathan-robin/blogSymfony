import {render, unmountComponentAtNode} from 'react-dom';
import React, {useEffect, useState, useRef, useCallback} from 'react';
import {usePaginatedFetch} from './hooks';
import {useFetch} from './hooks';
import {Field} from '../components/Form.jsx';

const dateFormat = {
    dateStyle:'medium', 
    timeStyle:'short'
}
const VIEW = 'VIEW'; 
const EDIT = 'EDIT';

function Comments({post, user}){ 
    console.log(post)
    const {items: comments, load, loading, count, hasMore, setItems} = usePaginatedFetch('/api/comments?article='+ post)

    const addComment = useCallback(comment => {
        setItems(comments => [comment, ...comments]);
    },[])
    const deleteComment = useCallback(comment => {
        setItems(comments => comments.filter(c => c !== comment));
    },[])
    const updateComment = useCallback((newComment, oldComment) => {
        setItems(comments => comments.map(c => c === oldComment ? newComment : c));
    },[])

    useEffect(() => { 
        load()
    },[])

    return <div>
                <Title count={count} />
                {comments.map(c => {
                    console.log(c);
                    console.log(user);
                    return <Comment key={c.id} comment={c} canEdit={c.authorId == user} post={post} onDelete={deleteComment} onUpdate={updateComment}/>
                }
                )}
                {hasMore && <button disabled={loading} className='btn btn-primary'onClick={load}>Charger plus de commentaires</button>}
                {user && <CommentForm post={post} user={user} onComment={addComment}/>}
            </div>
}

const Comment = React.memo(({post, comment, canEdit, onDelete, onUpdate}) => { 
    const [state, setState] = useState(VIEW)


    const toggleEdit = useCallback(() => {
        setState(state => state === VIEW ? EDIT:VIEW)
    },[state])

    const date = new Date(comment.createdAt);

    const onDeleteCallback = useCallback(()=>{ 
        onDelete(comment)
    },[comment])

    const onComment = useCallback((newComment) => {
        onUpdate(newComment, comment)
        toggleEdit()
    },[comment])



    const {loading:loadingDelete, load: callDelete} = useFetch(comment['@id'], 'DELETE', onDeleteCallback )
    
    console.log(state);
    return <div className="row">
        <h6 className="col-sm-3">
            <strong>{comment.author}</strong>
            <br/>commenté le <br/>
            <strong>{date.toLocaleString(undefined, dateFormat)}</strong>
        </h6>
        <div className="col-sm-9">
            <p>
                {state === VIEW ? <div dangerouslySetInnerHTML={{__html: comment.content}} />:
                <CommentForm post={post} onComment={onComment} comment={comment} onCancel={toggleEdit} />
            }
            </p>

            {canEdit && state !== EDIT && 
            <>
            <p>
                <button className="btn btn-danger" onClick={callDelete.bind(this, null)} disabled={loadingDelete}>
                    Supprimer
                </button>
            </p>
             <p>
                 <button className="btn btn-secondary" onClick={toggleEdit}>
                     Editer
                </button>
            </p></>}

        </div>
    </div>
})

const CommentForm = React.memo(({ post, user, username, onComment, comment = null, onCancel = null }) => {
    const ref = useRef(null)
    const onSuccess = useCallback(comment => {
        onComment(comment)
        ref.current.value = ''
    },[ref, onComment])

    const method = comment ? 'PUT' : 'POST'
    console.log(post)
    console.log(comment)
    const url = comment ? comment['@id'] : '/api/comments'

    const {load, loading, errors, clearError} = useFetch(url,method,onSuccess)
    const onSubmit = useCallback(e => {
        e.preventDefault()
        load({
            "content": ref.current.value,
            "article": "/api/articles/" + post,
            "createdAt" : new Date()
        })
    },[load, ref, post])

    useEffect(() => {
        if (comment && comment.content && ref.current){
            ref.current.value = comment.content
        }
    },[comment, ref])

    return <div className="well">
            <form onSubmit={onSubmit}>
                {comment === null && <fieldset>
                    <legend>Laisser un commentaire</legend>
                </fieldset>}
                <Field minLength={8} name='content' help="Les commentaires non conformes à notre code de conduite seront modérés." ref={ref}
                error={errors['content']} required onChange={clearError.bind(this, 'content')}>Votre commentaire </Field>
                <div className="form-group">
                        <button disabled={loading} className='btn btn-success'>
                           {comment === null ? 'Envoyer' : 'Editer'}
                        </button>
                        {onCancel && <button className='btn btn-danger' onClick={onCancel}>Annuler</button>}
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
    constructor(){
        super()
        this.observer = null
    }

    connectedCallback() {
        const post = parseInt(this.dataset.post, 10);
        const username = this.dataset.username;
        const user = parseInt(this.dataset.user, 10) || null ;
        if(this.observer === null){
            this.observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if(entry.isIntersecting && entry.target === this){
                        observer.disconnect()
                        render(<Comments username={username} user={user} post={post} />, this)
                    }
                })
            })
        }
        this.observer.observe(this)
    }
    disconnectedCallback () {
        if(this.observer){
            this.observer.disconnect()
        }
        unmountComponentAtNode(this)
    }

}
customElements.define('post-comments', CommentsElement)