import { useCustomMutation, useTranslate } from '@refinedev/core'
import {
  ConfirmButton,
  EditButtonModal,
  Main,
  Modal,
  StatusEmpty,
  useTable,
} from '@duxweb/dux-refine'
import { Card, Tag, Button, Image, Tooltip, Loading } from 'tdesign-react/esm'
import { Icon } from 'tdesign-icons-react'

const List = () => {
  const { data, refetch, loading } = useTable({})

  return (
    <Main>
      <Loading loading={loading} showOverlay>
        <div className='mb-4'>
          {data && data.length > 0 ? (
            <div className='grid grid-cols-2 gap-4 2xl:grid-cols-4 xl:grid-cols-3'>
              {data?.map((item, key) => <CardItem key={key} item={item} refetch={refetch} />)}
            </div>
          ) : (
            <Card>
              <StatusEmpty />
            </Card>
          )}
        </div>
      </Loading>
    </Main>
  )
}

interface CardItemProps {
  item?: Record<string, any>
  refetch?: () => void
}

const CardItem = ({ item, refetch }: CardItemProps) => {
  const { mutate } = useCustomMutation()
  const translate = useTranslate()

  return (
    <Card
      title={item?.name}
      actions={<Tag theme='success'>{translate('cms.theme.default')}</Tag>}
      bordered
      cover={<Image src={item?.image} className='h-50' fit='cover' position='top' />}
      footer={
        <div className='grid grid-cols-3 items-center justify-between divide-x divide-gray-200'>
          <div className='flex justify-center'>
            <Modal
              title={translate('cms.theme.info')}
              trigger={
                <Button variant='text'>
                  <Tooltip content={translate('cms.theme.info')}>
                    <Icon name='help-circle' />
                  </Tooltip>
                </Button>
              }
            >
              <div className='p-4'>{item?.help || translate('cms.theme.empty')}</div>
            </Modal>
          </div>
          <div className='flex justify-center'>
            <ConfirmButton
              onConfirm={() => {
                mutate({
                  url: `cms/theme/${item?.id}`,
                  method: 'patch',
                  values: {},
                  successNotification: () => {
                    refetch?.()
                    return false
                  },
                })
              }}
              variant='text'
              action='store'
            >
              <Tooltip content={translate('cms.theme.change')}>
                <Icon name='component-switch' />
              </Tooltip>
            </ConfirmButton>
          </div>
          <div className='flex justify-center'>
            <EditButtonModal
              component={() => import('./save')}
              rowId={item?.id}
              title={translate('cms.theme.config')}
              variant='text'
              theme='default'
            >
              <Tooltip content={translate('cms.theme.config')}>
                <Icon name='edit-1' />
              </Tooltip>
            </EditButtonModal>
          </div>
        </div>
      }
    ></Card>
  )
}

export default List
