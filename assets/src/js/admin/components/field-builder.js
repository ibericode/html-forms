import { h, Component } from 'preact';
import { FieldConfigurator } from './field-configurator.js';

class FieldBuilder extends Component {
  constructor(props) {
    super(props);

    this.state = {
      activeField: null,
    };

    this.handleCancel = this.handleCancel.bind(this);
    this.openFieldConfig = this.openFieldConfig.bind(this);
  }

  handleCancel() {
    this.setState({
      activeField: null,
    });
  }

  openFieldConfig(evt) {
    const field = this.props.fields[evt.target.value];

    if (this.state.activeField === field) {
      this.setState({ activeField: null });
    } else {
      this.setState({ activeField: field });
    }
  }

  render(props, state) {
    const fieldButtons = props.fields.map((f, i) => (
        <button type='button' key={i} value={i} className={`button ${state.activeField === f ? 'active' : ''}`} onClick={this.openFieldConfig}>{f.label}</button>
    ));
    const fieldType = state.activeField ? state.activeField.key : '';
    const rows = state.activeField ? state.activeField.configRows : [];

    return (
      <div className='hf-field-builder'>
        <h4>
          Add field
        </h4>
        <div className='available-fields'>
          {fieldButtons}
        </div>
        <div style='max-width: 480px;'>
          <FieldConfigurator fieldType={fieldType} rows={rows} onCancel={this.handleCancel} />
        </div>
        {state.activeField === null ? <p className='help' style='margin-bottom: 0;'>Use the buttons above to generate your field HTML, or manually modify your form below.</p> : ''}
      </div>
    );
  }
}

export { FieldBuilder };
