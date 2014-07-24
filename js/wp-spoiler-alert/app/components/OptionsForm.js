/** @jsx React.DOM */
var React        = require('react/addons');
var optionsStore = require('../app.js').optionsStore;

var OptionsForm = React.createClass({
  mixins: [React.addons.LinkedStateMixin],

  getInitialState: function() {
    return this.props.options;
  },

  handleSubmit: function(event) {
    event.preventDefault();
    this.props.noticeChange('progress', 'Saving settings ...');

    optionsStore.save(this.state)
      .then(this.updateState)
      .catch(this.showError);
  },

  handleReset: function(event) {
    event.preventDefault();
    this.props.noticeChange('progress', 'Restoring defaults ...');

    optionsStore.reset()
      .then(this.updateState)
      .catch(this.showError);
  },

  updateState: function() {
    this.setState(optionsStore.getOptions());
    this.props.noticeChange('success', 'Settings saved successfully.');
  },

  showError: function(error) {
    this.props.noticeChange('error', error);
  },

  render: function() {
    return (
      <form onSubmit={this.handleSubmit}>
        <table className="form-table">
          <tbody>
            <tr>
              <th scope="row">
                <label htmlFor="max">Maximum Blur</label>
              </th>
              <td>
                <input type="number" id="max" name="max" valueLink={this.linkState('max')} />
              </td>
            </tr>
            <tr>
              <th scope="row">
                <label htmlFor="partial">Partial Blur</label>
              </th>
              <td>
                <input type="number" id="partial" name="partial" valueLink={this.linkState('partial')} />
              </td>
            </tr>
            <tr>
              <th scope="row">
                <label htmlFor="tooltip">Tooltip</label>
              </th>
              <td>
                <input type="text" id="tooltip" name="tooltip" valueLink={this.linkState('tooltip')} />
              </td>
            </tr>
            <tr>
              <th scope="row">
                <label htmlFor="custom">Custom Stylesheet</label>
              </th>
              <td>
                <input type="checkbox" id="custom" name="custom" checkedLink={this.linkState('custom')} />
              </td>
            </tr>
          </tbody>
        </table>
        <p className="submit">
          <input name="submit" className="button button-primary" value="Save Changes" type="submit" />
          &nbsp;
          <input name="reset" className="button" value="Restore Defaults" type="submit" onClick={this.handleReset} />
        </p>
      </form>
    );
  }
});

module.exports = OptionsForm;
