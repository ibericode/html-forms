import { h, Component } from 'preact'
import { htmlgenerate } from '../field-builder/html.js'
import * as FS from './field-settings.js'
import linkState from 'linkstate'

class FieldConfigurator extends Component {
  constructor (props) {
    super(props)

    this.state = this.getInitialState()
    this.choiceHandlers = {
      add: this.addChoice.bind(this),
      delete: this.deleteChoice.bind(this),
      changeLabel: this.changeChoiceLabel.bind(this),
      toggleChecked: this.toggleChoiceChecked.bind(this)
    }
    this.addToForm = this.addToForm.bind(this)
    this.handleCancel = this.handleCancel.bind(this)
  }

  getInitialState () {
    return {
      formId: document.querySelector('input[name="form_id"]').value,
      formSlug: document.querySelector('input[name="form[slug]"]').value,
      fieldType: '',
      fieldLabel: '',
      placeholder: '',
      value: '',
      multiple: false,
      wrap: true,
      required: false,
      choices: [
        {
          checked: false,
          label: 'One'
        },
        {
          checked: false,
          label: 'Two'
        }
      ],
      accept: ''
    }
  }

  componentWillReceiveProps (props) {
    const newState = { fieldType: props.fieldType }

    // when changing from field that accepts multiple values to single-value field, reset all pre-selections
    if (this.state.fieldType === 'checkbox' && props.fieldType !== 'checkbox') {
      newState.choices = this.state.choices.map((c, i) => {
        c.checked = false
        return c
      })
    }
    this.setState(newState)
  }

  addToForm () {
    const html = htmlgenerate(this.state)
    window.html_forms.Editor.replaceSelection(html)
  }

  addChoice () {
    const arr = this.state.choices
    arr.push({ checked: false, label: '...' })
    this.setState({ choices: arr })
  }

  deleteChoice (e) {
    const arr = this.state.choices
    const index = e.target.parentElement.getAttribute('data-key')
    arr.splice(index, 1)
    this.setState({ choices: arr })
  }

  changeChoiceLabel (e) {
    const arr = this.state.choices
    const index = e.target.parentElement.getAttribute('data-key')
    arr[index].label = e.target.value
    this.setState({ choices: arr })
  }

  toggleChoiceChecked (e) {
    const arr = this.state.choices
    const index = e.target.parentElement.getAttribute('data-key')
    arr[index].checked = !arr[index].checked
    this.setState({ choices: arr })
  }

  static handleKeyPress (e) {
    // stop RETURN from submitting the parent form.
    if (e.keyCode === 13) {
      e.preventDefault()
    }
  }

  handleCancel () {
    // revert back to initial state
    this.setState(this.getInitialState())
    this.props.onCancel()
  }

  render (props, state) {
    if (props.rows.length === 0) {
      return ''
    }

    const formFields = []

    for (let i = 0; i < props.rows.length; i++) {
      switch (props.rows[i]) {
        case 'label':
          formFields.push(<FS.Label value={state.fieldLabel} onChange={linkState(this, 'fieldLabel')} />)
          break

        case 'placeholder':
          formFields.push(<FS.Placeholder value={state.placeholder} onChange={linkState(this, 'placeholder')} />)
          break

        case 'default-value':
          formFields.push(<FS.DefaultValue value={state.value} onChange={linkState(this, 'value')} />)
          break

        case 'multiple':
          formFields.push(<FS.Multiple checked={state.multiple} onChange={linkState(this, 'multiple')} />)
          break

        case 'required':
          formFields.push(<FS.Required checked={state.required} onChange={linkState(this, 'required')} />)
          break

        case 'wrap':
          formFields.push(<FS.Wrap checked={state.wrap} onChange={linkState(this, 'wrap')} />)
          break

        case 'add-to-form':
          formFields.push(<FS.AddToForm onSubmit={this.addToForm} onCancel={this.handleCancel} />)
          break

        case 'choices':
          formFields.push(<FS.Choices multiple={state.fieldType === 'checkbox'} choices={state.choices} handlers={this.choiceHandlers} />)
          break

        case 'button-text':
          formFields.push(<FS.ButtonText value={state.value} onChange={linkState(this, 'value')} />)
          break

        case 'accept':
          formFields.push(<FS.Accept value={state.accept} onChange={linkState(this, 'accept')} />)
          break
      }
    }

    return (
      <div class='field-config' onKeyPress={FieldConfigurator.handleKeyPress}>
        {formFields}
      </div>
    )
  }
}

export { FieldConfigurator }
