import { useTranslate } from '@refinedev/core'
import { FormModal } from '@duxweb/dux-refine'
import { Form, Input } from 'tdesign-react/esm'

const Page = (props: Record<string, any>) => {
  const translate = useTranslate()

  return (
    <FormModal id={props?.id} resource='tools.magicGroup'>
      <Form.FormItem
        label={translate('tools.magic.fields.groupName')}
        name='name'
        rules={[{ pattern: /^[a-zA-Z][a-zA-Z0-9_]*$/ }]}
        help={translate('tools.magic.help.groupName')}
      >
        <Input />
      </Form.FormItem>
      <Form.FormItem label={translate('tools.magic.fields.groupLabel')} name='label'>
        <Input />
      </Form.FormItem>
      <Form.FormItem
        label={translate('tools.magic.fields.groupIcon')}
        name='icon'
        help={
          <>
            {translate('tools.magic.help.groupIcon')}
            <a
              href='https://tdesign.tencent.com/react/components/icon'
              target='_blank'
              rel='noreferrer'
            >
              [{translate('tools.magic.help.groupIconDoc')}]
            </a>
          </>
        }
      >
        <Input />
      </Form.FormItem>
    </FormModal>
  )
}

export default Page
