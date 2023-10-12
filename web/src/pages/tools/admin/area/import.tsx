import { FormModal, useUpload } from '@duxweb/dux-refine'
import { useTranslate } from '@refinedev/core'
import { Form, Upload, Link } from 'tdesign-react/esm'

const Page = () => {
  const uploadParams = useUpload()
  const translate = useTranslate()

  return (
    <FormModal>
      <Form.FormItem
        name='file'
        help={
          <>
            <Link
              target='_blank'
              size='small'
              href='http://lbsyun.baidu.com/index.php?title=open/dev-res'
            >
              {translate('tools.area.help.import')}
            </Link>
          </>
        }
      >
        <Upload {...uploadParams} theme='file' draggable className='w-full' />
      </Form.FormItem>
    </FormModal>
  )
}

export default Page
