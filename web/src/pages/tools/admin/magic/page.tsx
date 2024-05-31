import { useTranslate, useResourceParams } from '@refinedev/core'
import { CascaderAsync, FormPage, FormPageItem } from '@duxweb/dux-refine'
import { Form, Input, Radio, Checkbox } from 'tdesign-react/esm'
import { MagicEditor } from '@duxweb/dux-extend'

const Page = () => {
  const translate = useTranslate()
  const { id } = useResourceParams()

  return (
    <FormPage
      formProps={{
        labelAlign: 'top',
      }}
      back
      id={id}
    >
      <FormPageItem
        name='group_id'
        label={translate('tools.magic.fields.group')}
        help={translate('tools.magic.help.group')}
      >
        <CascaderAsync
          checkStrictly
          url='tools.magicGroup'
          keys={{
            label: 'label',
            value: 'id',
          }}
          placeholder={translate('tools.magic.placeholder.group')}
          clearable
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
        help={translate('tools.magic.fields.nameDesc')}
      >
        <Input placeholder={translate('tools.magic.validate.name')} />
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

      <Form.FormItem shouldUpdate={(prev, next) => prev.type !== next.type}>
        {({ getFieldValue }) => {
          if (getFieldValue('type') === 'tree') {
            return (
              <FormPageItem
                name='tree_label'
                label={translate('tools.magic.fields.treeLabel')}
                help={translate('tools.magic.fields.treeLabelDesc')}
              >
                <Input />
              </FormPageItem>
            )
          }
          return <></>
        }}
      </Form.FormItem>

      <FormPageItem
        name='inline'
        label={translate('tools.magic.fields.affiliate')}
        help={translate('tools.magic.fields.affiliateDesc')}
        initialData={0}
      >
        <Radio.Group>
          <Radio value={0}>{translate('tools.magic.fields.independentType')}</Radio>
          <Radio value={1}>{translate('tools.magic.fields.inlineType')}</Radio>
        </Radio.Group>
      </FormPageItem>

      <FormPageItem
        name='external'
        label={translate('tools.magic.fields.external')}
        help={translate('tools.magic.fields.externalDesc')}
        initialData={0}
      >
        <Checkbox.Group
          options={[
            {
              value: 'read',
              label: translate('tools.magic.external.read'),
            },
            {
              value: 'create',
              label: translate('tools.magic.external.create'),
            },
            {
              value: 'edit',
              label: translate('tools.magic.external.edit'),
            },
          ]}
        />
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
