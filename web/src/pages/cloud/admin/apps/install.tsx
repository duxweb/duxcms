import { useCallback, useState } from 'react'
import { useTranslate } from '@refinedev/core'
import { Input, Button, MessagePlugin } from 'tdesign-react/esm'
import { Modal, useClient } from '@duxweb/dux-refine'

const Page = () => {
  const translate = useTranslate()
  const [url, setUrl] = useState('')
  const client = useClient()
  const [loading, setLoading] = useState(false)
  const [log, setLog] = useState('')

  const submit = useCallback((url: string) => {
    setLoading(true)
    client
      .request('cloud/apps/install', 'post', {
        data: {
          url: url,
        },
      })
      .then((res) => {
        if (res.statusCode !== 200) {
          MessagePlugin.error(res.message)
          return
        }
        setLog(res?.data?.content)
      })
      .finally(() => {
        setLoading(false)
      })
  }, [])

  return (
    <>
      <div className='p-4'>
        <div className='mb-2'>{translate('cloud.apps.validator.url')}</div>
        <Input
          value={url}
          placeholder='dux://'
          onChange={(value) => {
            setUrl(() => {
              return value
            })
            console.log(value)
          }}
        />
      </div>

      {log ? (
        <pre className='mt-2 overflow-auto rounded-lg p-4 bg-component'>{log}</pre>
      ) : (
        <Modal.Footer>
          <Button
            loading={loading}
            onClick={() => {
              submit(url)
            }}
          >
            {translate('cloud.apps.action.install')}
          </Button>
        </Modal.Footer>
      )}
    </>
  )
}

export default Page
