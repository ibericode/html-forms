import { h } from 'preact';

function AddToForm(props) {
  return (
    <div className='hf-small-margin'>
      <button className='button' type='button' onClick={props.onSubmit}>Add field to form</button> &nbsp; <a href='' className='hf-small' style='vertical-align: middle;' onClick={props.onCancel}>or close field helper</a>
    </div>
  );
}

function Label(props) {
  return (
    <div className='hf-small-margin'>
      <label htmlFor='hf-fg-field-label'>Field label <span className='hf-required'>*</span></label>
      <input id='hf-fg-field-label' type='text' value={props.value} onChange={props.onChange} />
    </div>
  );
}

function Placeholder(props) {
  return (
    <div className='hf-small-margin'>
      <label htmlFor='hf-fg-placeholder'>Placeholder <span className='hf-italic hf-pull-right'>Optional</span></label>
      <input id='hf-fg-placeholder' type='text' value={props.value} onChange={props.onChange} />
      <p className='description'>Text to show when field has no value.</p>
    </div>
  );
}

function ButtonText(props) {
  return (
    <div className='hf-small-margin'>
      <label htmlFor='hf-fg-default-value'>Button text <span className='hf-required'>*</span></label>
      <input id='hf-fg-default-value' type='text' value={props.value} onChange={props.onChange} />
      <p className='description'>Text to show on the button.</p>
    </div>
  );
}

function DefaultValue(props) {
  return (
    <div className='hf-small-margin'>
      <label htmlFor='hf-fg-default-value'>Default value <span className='hf-italic hf-pull-right'>Optional</span></label>
      <input id='hf-fg-default-value' type='text' value={props.value} onChange={props.onChange} />
      <p className='description'>Text to pre-fill this field with.</p>
    </div>
  );
}

function Multiple(props) {
  return (
    <div className='hf-small-margin'>
      <label className='inline'>
        <input type='checkbox' value='1' defaultChecked={props.checked} onChange={props.onChange} />
        Accept multiple values.
      </label>
    </div>
  );
}

function Wrap(props) {
  return (
    <div className='hf-small-margin'>
      <label className='inline'>
        <input type='checkbox' value='1' defaultChecked={props.checked} onChange={props.onChange} />
        Wrap this field in paragraph tags.
      </label>
    </div>
  );
}

function Required(props) {
  return (
    <div className='hf-small-margin'>
      <label className='inline'>
        <input type='checkbox' value='1' defaultChecked={props.checked} onChange={props.onChange} />
        This field is required.
      </label>
    </div>
  );
}

function Choices(props) {
  const choiceFields = props.choices.map((choice, k) => (
    <div data-key={k} key={k}>
      <input type={props.multiple ? 'checkbox' : 'radio'} name='selected' defaultChecked={choice.checked} onChange={props.handlers.toggleChecked} title='Pre-select this choice?' />
      <input type='text' value={choice.label} placeholder='Choice label' style='width: 80%;' onChange={props.handlers.changeLabel} />
      <a href='' onClick={props.handlers.delete} style='text-decoration: none;' title='Delete choice'>✕</a>
    </div>
  ));

  return (
    <div className='hf-small-margin'>
      <label>Choices</label>
      {choiceFields}
      <input type={props.multiple ? 'checkbox' : 'radio'} style='visibility: hidden;' /><a href='' onClick={props.handlers.add}>Add choice</a>
    </div>
  );
}

function Accept(props) {
  return (
    <div className='hf-small-margin'>
      <label>Accepted file types</label>
      <input type='text' value={props.value} onChange={props.onChange} />
      <p className='description'>Use a comma-separated list of accepted file extensions, eg <code>.pdf</code>. <br />Leave empty to accept any file type.</p>
    </div>
  );
}

export {
  AddToForm,
  Label,
  Placeholder,
  DefaultValue,
  Wrap,
  Required,
  Choices,
  ButtonText,
  Accept,
  Multiple,
};
