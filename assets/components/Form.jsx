import React from 'react'; 

const className= (...arr) => arr.filter(Boolean).join(' ')

export const Field = React.forwardRef((props, ref) => {

    if(props.error){ 
        props.help=props.error
    }

      return  <div className={className('form-group', props.error ? 'table-danger' : '')}>
          <label htmlFor={props.name} className='control-label'>{props.children}</label>
                <textarea ref={ref} name={props.name} id={props.name} cols="180" rows="10" className="from-control" onChange={props.onChange} required={props.required}
                minLength={props.minLength}>
                </textarea>
                {
                props.help && <div className='help-block'>
                    {props.help}
                    </div>
                    }
            </div>
})
