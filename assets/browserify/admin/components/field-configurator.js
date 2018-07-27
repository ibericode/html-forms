'use strict';

import { h, Component } from 'preact';
import { bind } from 'decko';
import { htmlgenerate } from '../field-builder/html.js';
import * as FS from './field-settings.js';
import linkState from 'linkstate';

class FieldConfigurator extends Component {
    constructor(props) {
        super(props);

        this.state = this.getInitialState();
        this.choiceHandlers = {
            "add": this.addChoice,
            "delete": this.deleteChoice,
            "changeLabel": this.changeChoiceLabel,
            "toggleChecked": this.toggleChoiceChecked,
        }
    }

    getInitialState() {
       return {
            formId: document.querySelector('input[name="form_id"]').value,
            formSlug: document.querySelector('input[name="form[slug]"]').value,
            fieldType: "",
            fieldLabel: "",
            placeholder: "",
            value: "",
            wrap: true,
            required: false,
            choices: [
                {
                    checked: false,
                    label: "One",
                },
                {
                    checked: false,
                    label: "Two",
                },
            ],
            accept: '',
       };
    }

    componentWillReceiveProps(props) { 
        let newState = { fieldType: props.fieldType };

        // when changing from field that accepts multiple values to single-value field, reset all pre-selections 
        if(this.state.fieldType === 'checkbox' && props.fieldType !== 'checkbox' ) {
            newState.choices = this.state.choices.map((c, i) => {
                c.checked = false; 
                return c; 
            });
        }
        this.setState(newState)
    }

    @bind
    addToForm() {
        const html = htmlgenerate(this.state);
        html_forms.Editor.replaceSelection(html);
    }

    @bind
    addChoice() {
        let arr = this.state.choices;
        arr.push({ checked: false, label: "..." });
        this.setState({choices: arr });
    }

    @bind
    deleteChoice(e) {
        let arr = this.state.choices;
        let index = e.target.parentElement.getAttribute('data-key');
        arr.splice(index, 1);
        this.setState({choices: arr });
    }

    @bind
    changeChoiceLabel(e) {
        let arr = this.state.choices;
        let index = e.target.parentElement.getAttribute('data-key');
        arr[index].label = e.target.value;
        this.setState({choices: arr });
    }

    @bind
    toggleChoiceChecked(e) {
        let arr = this.state.choices;
        let index = e.target.parentElement.getAttribute('data-key');
        arr[index].checked = !arr[index].checked;
        this.setState({choices: arr });
    }

    @bind
    static handleKeyPress(e) {
        // stop RETURN from submitting the parent form.
        if(e.keyCode === 13) {
            e.preventDefault();
        }
    }

    @bind
    handleCancel() {
        // revert back to initial state
        this.setState(this.getInitialState());
        this.props.onCancel();
    }

    render(props, state) {
        if(props.rows.length == 0) {
            return "";
        }

        let formFields = [];

        for(let i=0; i < props.rows.length; i++) {
            switch(props.rows[i]) {
                case "label":
                    formFields.push(<FS.Label value={state.fieldLabel} onChange={linkState(this, 'fieldLabel')}/>);
                break;

                case "placeholder":
                    formFields.push(<FS.Placeholder value={state.placeholder} onChange={linkState(this, 'placeholder')}/>);
                break;

                case "default-value":
                    formFields.push(<FS.DefaultValue value={state.value} onChange={linkState(this, 'value')}/>);
                break;

                case "required":
                    formFields.push(<FS.Required checked={state.required} onChange={linkState(this, 'required')}/>);
                break;

                case "wrap":
                    formFields.push(<FS.Wrap checked={state.wrap} onChange={linkState(this, 'wrap')}/>);
                break;

                case "add-to-form":
                    formFields.push(<FS.AddToForm onSubmit={this.addToForm} onCancel={this.handleCancel} />);
                break;

                case "choices":
                    formFields.push(<FS.Choices multiple={state.fieldType === 'checkbox'} choices={state.choices} handlers={this.choiceHandlers} />);
                break;

                case "button-text":
                    formFields.push(<FS.ButtonText value={state.value} onChange={linkState(this, 'value')}/>);
                break;

                case "accept":
                    formFields.push(<FS.Accept value={state.accept} onChange={linkState(this, 'accept')}/>);
                break;

            }
        }

        return (
            <div class="field-config" onKeyPress={FieldConfigurator.handleKeyPress}>
                {formFields}
            </div>
        )
    }
}

export { FieldConfigurator }
