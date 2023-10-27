import { useTranslate, useResource } from '@refinedev/core'
import {
  FormPage,
  formatUploadSingle,
  getUploadSingle,
  FormPageItem,
  useSelect,
} from '@duxweb/dux-refine'
import { Input, Radio, Select } from 'tdesign-react/esm'
import { MagicEditor } from '@duxweb/dux-extend'

const Page = () => {
  const translate = useTranslate()
  const { id } = useResource()
  const { options, onSearch, queryResult } = useSelect({
    resource: 'tools.magicGroup',
    optionLabel: 'label',
    optionValue: 'id',
  })

  return (
    <FormPage
      formProps={{
        labelAlign: 'top',
      }}
      back
      id={id}
      initFormat={(data) => {
        data.image = formatUploadSingle(data.image)
        return data
      }}
      saveFormat={(data) => {
        data.image = getUploadSingle(data.image)
        return data
      }}
    >
      <FormPageItem
        name='group_id'
        label={translate('tools.magic.fields.group')}
        help={translate('tools.magic.help.group')}
      >
        <Select
          filterable
          clearable
          onSearch={onSearch}
          options={options}
          placeholder={translate('tools.magic.placeholder.group')}
          loading={queryResult.isLoading}
        />
      </FormPageItem>
      <FormPageItem
        name='label'
        label={translate('tools.magic.fields.label')}
        help={translate('tools.magic.fields.labelDesc')}
      >
        <Input placeholder={translate('tools.magic.validate.label')} />
      </FormPageItem>
      <FormPageItem
        name='name'
        label={translate('tools.magic.fields.name')}
        help={translate('tools.magic.fields.labelDesc')}
      >
        <Input placeholder={translate('tools.magic.validate.name')} />
      </FormPageItem>

      <FormPageItem
        name='type'
        label={translate('tools.magic.fields.type')}
        help={translate('tools.magic.fields.typeDesc')}
        initialData='common'
      >
        <Radio.Group>
          <Radio value='common'>{translate('tools.magic.fields.list')}</Radio>
          <Radio value='pages'>{translate('tools.magic.fields.pages')}</Radio>
          <Radio value='tree'>{translate('tools.magic.fields.tree')}</Radio>
          <Radio value='page'>{translate('tools.magic.fields.page')}</Radio>
        </Radio.Group>
      </FormPageItem>

      <FormPageItem
        name='external'
        label={translate('tools.magic.fields.external')}
        help={translate('tools.magic.fields.externalDesc')}
        initialData={0}
      >
        <Radio.Group>
          <Radio value={0}>{translate('tools.magic.fields.public')}</Radio>
          <Radio value={1}>{translate('tools.magic.fields.private')}</Radio>
        </Radio.Group>
      </FormPageItem>

      <FormPageItem
        name='page'
        label={translate('tools.magic.fields.option')}
        help={translate('tools.magic.fields.optionDesc')}
        initialData={0}
      >
        <Radio.Group>
          <Radio value={0}>{translate('tools.magic.fields.modal')}</Radio>
          <Radio value={1}>{translate('tools.magic.fields.page')}</Radio>
        </Radio.Group>
      </FormPageItem>

      <FormPageItem
        name='fields'
        label={translate('tools.magic.fields.fields')}
        help={translate('tools.magic.fields.fieldsDesc')}
      >
        <MagicEditor />
      </FormPageItem>
    </FormPage>
  )
}

export default Page
